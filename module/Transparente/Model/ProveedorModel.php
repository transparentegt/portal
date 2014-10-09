<?php
namespace Transparente\Model;

class ProveedorModel extends AbstractModel
{
    public function findAll($where = [], $orderBy = [])
    {
        if (!$orderBy) {
            $orderBy = ['nombre' => 'ASC'];
        }
        return $this->findBy($where, $orderBy);
    }

    public function findByNoDomicilioFiscal()
    {
        $dql = 'SELECT Proveedor
                FROM Transparente\Model\Entity\Proveedor Proveedor
                WHERE Proveedor.domicilio_fiscal IS NULL
                ORDER BY Proveedor.nombre
                ';
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();

    }

    /**
     * Retornla el arreglo de proveedores pendientes de leer sus proyectos
     * 
     * @return Proveedor[]
     */
    public function findPendientesDeScrapearProyectos()
    {
        $dql    = 'SELECT MAX(Proveedor.id)
                FROM Transparente\Model\Entity\Proveedor Proveedor
                JOIN Proveedor.proyectos
                ';
        $query  = $this->getEntityManager()->createQuery($dql);
        $result = $query->getResult();
        $result = (int) $result[0][1];
        $dql    = "SELECT Proveedor
                FROM Transparente\Model\Entity\Proveedor Proveedor
                WHERE Proveedor.id >= $result
                ORDER BY Proveedor.id ASC";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }

    /**
     * Lee todos los datos del proveedor según su ID
     *
     * @param int $id
     * @return array
     */
    public function scrap($id)
    {
        $url    = "http://guatecompras.gt/proveedores/consultaDetProvee.aspx?rqp=8&lprv={$id}";
        $página = ScraperModel::getCachedUrl($url, "proveedor-{$id}");

        /**
         * Que valores vamos a buscar via xpath en la página del proveedor
         *
         * Usamos de nombre los campos de la base de datos para después solo volcar el arreglo con los resultados directo a
         * la DB.
         *
         * @var array
         */
        $xpaths = [
            'email'                => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioComercial2"]//tr[2]//td[2]',
            'nit'                  => '//*[@id="MasterGC_ContentBlockHolder_lblNIT"]',
            'nombre'               => '//*[@id="MasterGC_ContentBlockHolder_lblNombreProv"]',
            'principal_actividad'  => '//*[@id="MasterGC_ContentBlockHolder_pnl_TrabActiv2"]//tr[1]//td[2]',
            'principal_trabajo'    => '//*[@id="MasterGC_ContentBlockHolder_pnl_TrabActiv2"]//tr[2]//td[2]',
            'rep_legales_updated'  => '//*[@id="MasterGC_ContentBlockHolder_divRepresentantesLegales"]//span/span',
            'status'               => '//*[@id="MasterGC_ContentBlockHolder_lblHabilitado"]',
            'tiene_acceso_sistema' => '//*[@id="MasterGC_ContentBlockHolder_lblContraSnl"]',
            'tipo_organización'    => '//*[@id="MasterGC_ContentBlockHolder_pnl_DatosInscripcion2"]//tr[1]//td[2]',
            'fiscal'     => [
                'updated'      => 'div#MasterGC_ContentBlockHolder_divDomicilioFiscal span.AvisoGrande span.AvisoGrande',
                'departamento' => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioFiscal2"]//tr[1]//td[2]',
                'municipio'    => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioFiscal2"]//tr[2]//td[2]',
                'direccion'    => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioFiscal2"]//tr[3]//td[2]',
                'telefonos'    => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioFiscal2"]//tr[4]//td[2]',
                'fax'          => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioFiscal2"]//tr[5]//td[2]',
            ],
            'comercial'     => [
                'updated'      => null,
                'departamento' => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioComercial2"]//tr[3]//td[2]',
                'municipio'    => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioComercial2"]//tr[4]//td[2]',
                'direccion'    => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioComercial2"]//tr[5]//td[2]',
                'telefonos'    => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioComercial2"]//tr[6]//td[2]',
                'fax'          => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioComercial2"]//tr[7]//td[2]',
            ],
            'url'                 => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioComercial2"]//tr[1]//td[2]',
            'updated_sat'         => '//*[@id="MasterGC_ContentBlockHolder_lblFechaInfo"]',
        ];

        $proveedor = ['id' => $id] + ScraperModel::fetchData($xpaths, $página);

        // después de capturar los datos, hacemos un postproceso

        $proveedor['status']               = ($proveedor['status'] == 'HABILITADO');
        $proveedor['tiene_acceso_sistema'] = ($proveedor['tiene_acceso_sistema'] == 'CON CONTRASEÑA');
        // descartar direcciones vacías
        if ($proveedor['fiscal']['direccion'] == '[--No Especificado--]' ||
            $proveedor['fiscal']['municipio'] == '[--No Especificado--]') {
            unset($proveedor['fiscal']);
        }
        if ($proveedor['comercial']['direccion'] == '[--No Especificado--]' ||
            $proveedor['comercial']['municipio'] == '[--No Especificado--]') {
            unset($proveedor['comercial']);
        }

        // algunas fechas no están bien parseadas
        $proveedor['rep_legales_updated']  = strptime($proveedor['rep_legales_updated'], '(Datos recibidos de la SAT el: %d.%b.%Y %T ');
        $proveedor['rep_legales_updated']  = 1900+$proveedor['rep_legales_updated']['tm_year']
                                            . '-' . (1 + $proveedor['rep_legales_updated']['tm_mon'])
                                            . '-' . ($proveedor['rep_legales_updated']['tm_mday'])
                                            ;
        $proveedor['url']   = ($proveedor['url']   != '[--No Especificado--]') ? $proveedor['url'] : null;
        $proveedor['email'] = ($proveedor['email'] != '[--No Especificado--]') ? $proveedor['email'] : null;
        return $proveedor;
    }

    /**
     * Conseguir todos los proveedores adjudicados del año en curso
     *
     * @return int[]
     *
     * @todo Quedan solo 2 $pagerKeys que debeoms de sacar de la primera página para quitar ese parámetro
     *
     * @todo Esto es TAN igual al ProyectoModel->scrapList() que deberíamos de moverlo a la superclase
     */
    public function scrapList()
    {
        $pagerKeys   = [
            '_body:MasterGC$ContentBlockHolder$ScriptManager1' => 'MasterGC$ContentBlockHolder$UpdatePanel1|MasterGC$ContentBlockHolder$dgResultado$ctl54$ctl',
            '__EVENTTARGET'                                    => 'MasterGC$ContentBlockHolder$dgResultado$ctl54$ctl',
        ];
        $ids   = [];
        $page  = 0;
        $start = 'http://guatecompras.gt/proveedores/consultaProveeAdjLst.aspx?lper='.date('Y');
        do {
            $page++;
            $html  = ScraperModel::getCachedUrl($start, "proveedores-$page", ScraperModel::PAGE_MODE_PAGER, $pagerKeys);
            $xpath = "//a[starts-with(@href, './consultaDetProveeAdj.aspx')]";
            $list  = $html->queryXpath($xpath);
            foreach ($list as $nodo) {
                /* @var $proveedor DOMElement */
                $url = parse_url($nodo->getAttribute('href'));
                parse_str($url['query'], $url);
                $id   = (int) $url['lprv'];
                if (!in_array($id, $ids)) {
                    $ids[] = $id;
                }
            }
        } while ($page <= $pagerKeys['totalPages']);
        sort($ids);
        return $ids;
    }

    /**
     * Obtiene los nombres comerciales de los proveedores
     *
     * @param int $id
     * @return array
     */
    public function scrapNombresComerciales($id)
    {
        $url     = 'http://guatecompras.gt/proveedores/consultaProveeNomCom.aspx?rqp=8&lprv='.$id;
        $página  = ScraperModel::getCachedUrl($url, "nombre-comercial-$id");
        $xpath   = '//*[@id="MasterGC_ContentBlockHolder_dgResultado"]//tr[not(@class="TablaTitulo")]/td[2]';
        $nodos   = $página->queryXpath($xpath);
        $nombres = [];
        foreach ($nodos as $nodo) {
            $nombre = $nodo->nodeValue;
            if (in_array($nombre, $nombres)) continue;
            $nombres[] = $nombre;
        }
        sort($nombres);
        return $nombres;
    }
}