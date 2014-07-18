<?php
namespace Transparente\Model;

use Doctrine\ORM\EntityRepository;
use Transparente\Model\Entity\Proveedor;

class ProveedorModel extends EntityRepository
{
    private function humanizarNombreDeEmpresa($nombre)
    {
        $nombre  = str_replace('SOCIEDAD ANONIMA', 'S.A.', $nombre);
        $nombres = preg_split('/[\s,]+/', mb_strtolower($nombre, 'UTF-8'));
        foreach ($nombres as $key => $nombre) {
            if (preg_match('/\./', $nombre)) {
                $nombres[$key] = strtoupper($nombre);
            } else {
                $nombres[$key] = ucfirst($nombre);
            }
        }
        $nombre = implode(' ', $nombres);
        return $nombre;
    }

    public function findAll()
    {
        return $this->findBy($criteria = [], $orderBy = ['nombre' => 'ASC']);
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

    public function save(Proveedor $entity)
    {
        $em = $this->getEntityManager();
        $em->persist($entity);
    }

    /**
     * Lee todos los datos del proveedor según su ID
     *
     * @param int $id
     * @return array
     */
    public function scrap($id)
    {
        $url               = "http://guatecompras.gt/proveedores/consultaDetProvee.aspx?rqp=8&lprv={$id}";
        $páginaDelProveedor = ScraperModel::getCachedUrl($url);

        /**
         * Que valores vamos a buscar via xpath en la página del proveedor
         *
         * Usamos de nombre los campos de la base de datos para después solo volcar el arreglo con los resultados directo a
         * la DB.
         *
         * @var array
         */
        $xpaths = [
            'nombre'               => '//*[@id="MasterGC_ContentBlockHolder_lblNombreProv"]',
            'nit'                  => '//*[@id="MasterGC_ContentBlockHolder_lblNIT"]',
            'status'               => '//*[@id="MasterGC_ContentBlockHolder_lblHabilitado"]',
            'tiene_acceso_sistema' => '//*[@id="MasterGC_ContentBlockHolder_lblContraSnl"]',
            'domicilio_fiscal'     => [
                'updated'      => 'div#MasterGC_ContentBlockHolder_divDomicilioFiscal span.AvisoGrande span.AvisoGrande',
                'departamento' => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioFiscal2"]//tr[1]//td[2]',
                'municipio'    => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioFiscal2"]//tr[2]//td[2]',
                'direccion'    => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioFiscal2"]//tr[3]//td[2]',
                'telefonos'    => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioFiscal2"]//tr[4]//td[2]',
                'fax'          => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioFiscal2"]//tr[5]//td[2]',
            ],
            'domicilio_comercial'     => [
                'updated'      => null,
                'departamento' => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioComercial2"]//tr[3]//td[2]',
                'municipio'    => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioComercial2"]//tr[4]//td[2]',
                'direccion'    => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioComercial2"]//tr[5]//td[2]',
                'telefonos'    => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioComercial2"]//tr[6]//td[2]',
                'fax'          => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioComercial2"]//tr[7]//td[2]',
            ],
            'url'                 => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioComercial2"]//tr[1]//td[2]',
            'email'               => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioComercial2"]//tr[2]//td[2]',
            'rep_legales_updated' => '//*[@id="MasterGC_ContentBlockHolder_divRepresentantesLegales"]//span/span',
        ];

        $proveedor = ['id' => $id] + ScraperModel::fetchData($xpaths, $páginaDelProveedor);

        // después de capturar los datos, hacemos un postproceso

        $proveedor['nombre']               = $this->humanizarNombreDeEmpresa($proveedor['nombre']);
        $proveedor['status']               = ($proveedor['status'] == 'HABILITADO');
        $proveedor['tiene_acceso_sistema'] = ($proveedor['tiene_acceso_sistema'] == 'CON CONTRASEÑA');
        // descartar direcciones vacías
        if ($proveedor['domicilio_fiscal']['direccion'] == '[--No Especificado--]' ||
            $proveedor['domicilio_fiscal']['municipio'] == '[--No Especificado--]') {
            unset($proveedor['domicilio_fiscal']);
        }
        if ($proveedor['domicilio_comercial']['direccion'] == '[--No Especificado--]' ||
            $proveedor['domicilio_comercial']['municipio'] == '[--No Especificado--]') {
            unset($proveedor['domicilio_comercial']);
        }

        // algunas fechas no están bien parseadas
        $proveedor['rep_legales_updated']  = strptime($proveedor['rep_legales_updated'], '(Datos recibidos de la SAT el: %d.%b.%Y %T ');
        $proveedor['rep_legales_updated']  = 1900+$proveedor['rep_legales_updated']['tm_year']
                                            . '-' . (1 + $proveedor['rep_legales_updated']['tm_mon'])
                                            . '-' . ($proveedor['rep_legales_updated']['tm_mday'])
                                            ;
        $proveedor['url']                  = ($proveedor['url']   != '[--No Especificado--]') ? $proveedor['url'] : null;
        $proveedor['email']                = ($proveedor['email'] != '[--No Especificado--]') ? $proveedor['email'] : null;
        return $proveedor;
    }

    /**
     * Obtiene los nombres comerciales de los proveedores
     *
     * @param int $id
     * @return array
     */
    public function scrapNombresComerciales($id)
    {
        $página  = ScraperModel::getCachedUrl('http://guatecompras.gt/proveedores/consultaProveeNomCom.aspx?rqp=8&lprv='.$id);
        $xpath   = '//*[@id="MasterGC_ContentBlockHolder_dgResultado"]//tr[not(@class="TablaTitulo")]/td[2]';
        $nodos   = $página->queryXpath($xpath);
        $nombres = [];
        foreach ($nodos as $nodo) {
            $nombre = $this->humanizarNombreDeEmpresa($nodo->nodeValue);
            if (in_array($nombre, $nombres)) continue;
            $nombres[] = $nombre;
        }
        sort($nombres);
        return $nombres;
    }
}