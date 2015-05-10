<?php
namespace Transparente\Model;

use Transparente\Model\Entity\Proveedor;
class ProveedorModel extends AbstractModel
{
    public function findAll($where = [], $orderBy = [])
    {
        if (!$orderBy) {
            $orderBy = ['nombre' => 'ASC'];
        }
        return $this->findBy($where, $orderBy);
    }

    public function getPagerSinDomicilioFiscal()
    {
        $dql = 'SELECT Proveedor
                FROM Transparente\Model\Entity\Proveedor Proveedor
                WHERE Proveedor.domicilio_fiscal IS NULL
                ORDER BY Proveedor.nombre
                ';
        $paginator = $this->getPaginatorFromDql($dql);
        return $paginator;

    }

    /**
     * Retornla el arreglo de proveedores pendientes de leer sus representantes legales
     *
     * @return Proveedor[]
     */
    public function findPendientesDeScrapearProyectos()
    {
        $dql    = 'SELECT Proveedor.id
                    FROM Transparente\Model\Entity\Proveedor Proveedor
                    JOIN Proveedor.pagos Pago
                    ORDER BY Pago.id DESC
                    ';
        $query  = $this->getEntityManager()->createQuery($dql);
        $query->setMaxResults(1);
        $result = $query->getScalarResult();
        $result = (int) (!empty($result[0]['id'])) ? $result[0]['id'] : 0;
        $dql    = "SELECT Proveedor
                    FROM Transparente\Model\Entity\Proveedor Proveedor
                    WHERE Proveedor.id >= $result
                    ORDER BY Proveedor.id ASC";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }

    /**
     * Retorna el arreglo de proveedores sin representantes legales
     *
     * @return Proveedor[]
     * @todo creo que ya no se usa
     */
    public function findPendientesDeScrapearRepresentantesLegales()
    {
        $dql = 'SELECT Proveedor FROM Transparente\Model\Entity\Proveedor Proveedor
                LEFT JOIN Proveedor.representantes_legales RepresentanteLegal
                WHERE RepresentanteLegal.id IS NULL
                ORDER BY RepresentanteLegal.id ASC
                ';
        $query = $this->getEntityManager()->createQuery($dql);
        $rs    = $query->getResult();
        return $rs;
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
            'email'                          => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioComercial2"]//tr[2]//td[2]',
            'inscripción_fecha_constitución' => '//*[@id="MasterGC_ContentBlockHolder_pnl_DatosInscripcion2"]//tr[3]//td[2]',
            'inscripción_fecha_definitiva'   => '//*[@id="MasterGC_ContentBlockHolder_pnl_DatosInscripcion2"]//tr[5]//td[2]',
            'inscripción_fecha_provisional'  => '//*[@id="MasterGC_ContentBlockHolder_pnl_DatosInscripcion2"]//tr[4]//td[2]',
            'inscripción_fecha_sat'          => '//*[@id="MasterGC_ContentBlockHolder_pnl_DatosInscripcion2"]//tr[6]//td[2]',
            'inscripción_número_escritura'   => '//*[@id="MasterGC_ContentBlockHolder_pnl_DatosInscripcion2"]//tr[2]//td[2]',
            'nit'                            => '//*[@id="MasterGC_ContentBlockHolder_lblNIT"]',
            'nombre'                         => '//*[@id="MasterGC_ContentBlockHolder_lblNombreProv"]',
            'principal_actividad'            => '//*[@id="MasterGC_ContentBlockHolder_pnl_TrabActiv2"]//tr[1]//td[2]',
            'principal_trabajo'              => '//*[@id="MasterGC_ContentBlockHolder_pnl_TrabActiv2"]//tr[2]//td[2]',
            'rep_legales_updated'            => '//*[@id="MasterGC_ContentBlockHolder_divRepresentantesLegales"]//span/span',
            'status'                         => '//*[@id="MasterGC_ContentBlockHolder_lblHabilitado"]',
            'tiene_acceso_sistema'           => '//*[@id="MasterGC_ContentBlockHolder_lblContraSnl"]',
            'tipo_organización'              => '//*[@id="MasterGC_ContentBlockHolder_pnl_DatosInscripcion2"]//tr[1]//td[2]',
            'updated_sat'                    => '//*[@id="MasterGC_ContentBlockHolder_lblFechaInfo"]',
            'url'                            => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioComercial2"]//tr[1]//td[2]',
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
        ];
        $proveedor = ['id' => $id] + ScraperModel::fetchData($xpaths, $página);
        // descartar direcciones vacías
        if ($proveedor['fiscal']['direccion'] == '[--No Especificado--]' ||
            $proveedor['fiscal']['municipio'] == '[--No Especificado--]') {
            unset($proveedor['fiscal']);
        }
        if ($proveedor['comercial']['direccion'] == '[--No Especificado--]' ||
            $proveedor['comercial']['municipio'] == '[--No Especificado--]') {
            unset($proveedor['comercial']);
        }
        return $proveedor;
    }

    /**
     * Conseguir todos los proveedores adjudicados de todo el sistema
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
        $ids      = [];
        $page     = 0;
        $start    = 'http://guatecompras.gt/proveedores/consultaProveeAdjLst.aspx';
        $último   = $this->findOneBy([], ['id' => 'DESC']);
        if ($último) {
            $últimoId = $último->getId();
            $db       = $this->getEntityManager();
            $db->remove($último);
            $db->flush();
            $db->clear();
        }
        do {
            $page++;
            $html  = ScraperModel::getCachedUrl($start, "proveedores-$page", ScraperModel::PAGE_MODE_PAGER, $pagerKeys);
            $xpath = "//a[starts-with(@href, './consultaDetProveeAdj.aspx')]";
            $list  = $html->queryXpath($xpath);
            foreach ($list as $nodo) {
                /* @var $proveedor DOMElement */
                $url = parse_url($nodo->getAttribute('href'));
                parse_str($url['query'], $url);
                $id       = (int) $url['lprv'];
                if (($último === null) || $id >= $últimoId) {
                    $ids[$id] = $id;
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

    /**
     * Paginar los datos del proveedor
     *
     * @return Paginator
     */
    public function getPaginator(\Zend\Stdlib\Parameters $params = null)
    {
        $queryOptions = [
            'order'  => 'Proveedor.nombre',
            'sort'   => 'ASC',
            'filter' => false,
        ];
        if ($params) {
            $queryOptions = array_merge($queryOptions, $params->toArray());
        }

        $dql = 'SELECT Proveedor FROM Transparente\Model\Entity\Proveedor Proveedor';
        if ($queryOptions['filter']) {
            $dql .= "
                WHERE Proveedor.nombre LIKE '%{$queryOptions['filter']}%'
                   OR Proveedor.nit    LIKE '%{$queryOptions['filter']}%'
            ";
        }
        $dql .= " ORDER BY {$queryOptions['order']} {$queryOptions['sort']}";
        $paginator = $this->getPaginatorFromDql($dql);
        return $paginator;
    }

}