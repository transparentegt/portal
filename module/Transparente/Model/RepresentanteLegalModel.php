<?php
namespace Transparente\Model;

use Transparente\Model\Entity\EmpleadoMunicipal;
use Transparente\Model\Entity\RepresentanteLegal;

class RepresentanteLegalModel extends AbstractModel
{
    protected static $scraped = [];

    /**
     * Retorna todos los representantes legales
     *
     * @return Transparente\Model\Entity\RepresentanteLegal[]
     */
    public function findAll()
    {
        $rs = $this->findBy($criteria = [], $orderBy = [
            'apellido1' => 'ASC',
            'apellido2' => 'ASC',
            'nombre1'   => 'ASC',
            'nombre2'   => 'ASC',
        ]);
        return $rs;
    }

    /**
     * Buscar todos los representantes legales que tengan apellidos relacionados al empleado municipal
     *
     * @param EmpleadoMunicipal $empleado
     *
     * @return Transparente\Model\Entity\RepresentanteLegal[]
     */
    public function findByEmpleadoMunicipal(EmpleadoMunicipal $empleado)
    {
        $dql = 'SELECT RepresentanteLegal
                FROM \Transparente\Model\Entity\EmpleadoMunicipal  EmpleadoMunicipal
                JOIN \Transparente\Model\Entity\RepresentanteLegal RepresentanteLegal
                WHERE EmpleadoMunicipal.id = ?1
                    AND (
                            (
                                    RepresentanteLegal.apellido1 IS NOT NULL
                                AND RepresentanteLegal.apellido1 <> \'\'
                                AND (
                                       (RepresentanteLegal.apellido1 = EmpleadoMunicipal.apellido1)
                                    OR (RepresentanteLegal.apellido1 = EmpleadoMunicipal.apellido2)
                                    OR (RepresentanteLegal.apellido1 = EmpleadoMunicipal.apellido3)
                                )
                            ) OR (
                                    RepresentanteLegal.apellido2 IS NOT NULL
                                AND RepresentanteLegal.apellido2 <> \'\'
                                AND (
                                       (RepresentanteLegal.apellido2 = EmpleadoMunicipal.apellido1)
                                    OR (RepresentanteLegal.apellido2 = EmpleadoMunicipal.apellido2)
                                    OR (RepresentanteLegal.apellido2 = EmpleadoMunicipal.apellido3)
                                )
                            ) OR (
                                    RepresentanteLegal.apellido3 IS NOT NULL
                                AND RepresentanteLegal.apellido3 <> \'\'
                                AND (
                                       (RepresentanteLegal.apellido3 = EmpleadoMunicipal.apellido1)
                                    OR (RepresentanteLegal.apellido3 = EmpleadoMunicipal.apellido2)
                                    OR (RepresentanteLegal.apellido3 = EmpleadoMunicipal.apellido3)
                                )
                            )


                    )
                ORDER BY RepresentanteLegal.apellido1, RepresentanteLegal.apellido2,
                         RepresentanteLegal.nombre1,   RepresentanteLegal.nombre2
                ';
        $rs = $this->getEntityManager()->createQuery($dql)
                ->setParameter(1, $empleado->getId())
                ->getResult();
        return $rs;
    }

    /**
     * Genera el reporte de los representantes legales que tienen representantes legales
     *
     * @return Transparente\Model\Entity\RepresentanteLegal[]
     */
    public function findByMultiLevel()
    {
        $dql = 'SELECT RepresentanteLegal
                FROM Transparente\Model\Entity\RepresentanteLegal  RepresentanteLegal
                JOIN RepresentanteLegal.representantes_legales     SubRepresentantes
                ORDER BY
                    RepresentanteLegal.apellido1 ASC,
                    RepresentanteLegal.apellido2 ASC,
                    RepresentanteLegal.nombre1 ASC,
                    RepresentanteLegal.nombre2 ASC
                ';
        $rs = $this->getEntityManager()->createQuery($dql)->execute();
        return $rs;
    }

    /**
     * Genera el reporte de los representantes legales asociados a más de un proveedor
     *
     * @return Transparente\Model\Entity\RepresentanteLegal[]
     */
    public function findByMultiProveedor()
    {
        $dql = 'SELECT RepresentanteLegal
                FROM Transparente\Model\Entity\RepresentanteLegal  RepresentanteLegal
                JOIN RepresentanteLegal.proveedores                Proveedor
                GROUP BY RepresentanteLegal
                HAVING COUNT(Proveedor) > 1
                ORDER BY
                    RepresentanteLegal.apellido1 ASC,
                    RepresentanteLegal.apellido2 ASC,
                    RepresentanteLegal.nombre1 ASC,
                    RepresentanteLegal.nombre2 ASC
                ';
        $rs = $this->getEntityManager()->createQuery($dql)->execute();
        return $rs;
    }

    /**
     * Genera el reporte de los representantes legales con nombres comerciales
     *
     * @return Transparente\Model\Entity\RepresentanteLegal[]
     */
    public function findByNombresComerciales()
    {
        $dql = 'SELECT RepresentanteLegal
                FROM Transparente\Model\Entity\RepresentanteLegal  RepresentanteLegal
                JOIN RepresentanteLegal.nombres_comerciales        NombreComercial
                GROUP BY RepresentanteLegal
                HAVING COUNT(NombreComercial) > 1
                ORDER BY
                    RepresentanteLegal.apellido1 ASC,
                    RepresentanteLegal.apellido2 ASC,
                    RepresentanteLegal.nombre1 ASC,
                    RepresentanteLegal.nombre2 ASC
                ';
        $rs = $this->getEntityManager()->createQuery($dql)->execute();
        return $rs;
    }

    /**
     * Partir el nombre para guardarlo ordenadamente.
     *
     * En GTC está en formato "$apellido1, $apellido2, $apellido3?, $nombre1, $nombre2"
     *
     * @param array $data
     */
    private function splitNombre(&$data)
    {
        $nombres = explode(',', $data['nombre']);
        if (count($nombres) != 5) throw new \Exception('Formato de nombre inválido');
        $data['nombre1']   = $nombres[3];
        $data['nombre2']   = $nombres[4];
        $data['apellido1'] = $nombres[0];
        $data['apellido2'] = $nombres[1];
        $data['apellido3'] = $nombres[2];
        unset($data['nombre']);
    }

    /**
     * Lee todos los datos según su ID
     *
     * @param int $id
     *
     * @return RepresentanteLegal
     */
    public function scrap($id)
    {
        if (isset(self::$scraped[$id])) {
            return self::$scraped[$id];
        }

        $url    = "http://guatecompras.gt/proveedores/consultaDetProvee.aspx?rqp=10&lprv={$id}";
        $página = ScraperModel::getCachedUrl($url, "representante-legal-$id");
        $xpaths = [
            'nombre'               => '//*[@id="MasterGC_ContentBlockHolder_lblNombreProv"]',
            'nit'                  => '//*[@id="MasterGC_ContentBlockHolder_lblNIT"]',
            'status'               => '//*[@id="MasterGC_ContentBlockHolder_lblHabilitado"]',
            'tiene_acceso_sistema' => '//*[@id="MasterGC_ContentBlockHolder_lblContraSnl"]',
            'fiscal' => [
                'updated'      => 'div#MasterGC_ContentBlockHolder_divDomicilioFiscal span.AvisoGrande span.AvisoGrande',
                'departamento' => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioFiscal2"]//tr[1]//td[2]',
                'municipio'    => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioFiscal2"]//tr[2]//td[2]',
                'direccion'    => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioFiscal2"]//tr[3]//td[2]',
                'telefonos'    => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioFiscal2"]//tr[4]//td[2]',
                'fax'          => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioFiscal2"]//tr[5]//td[2]',
            ],
            'comercial' => [
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

        $data = ['id' => $id] + ScraperModel::fetchData($xpaths, $página);
        try {
            $this->splitNombre($data);
        } catch (\Exception $e) {
            return false;
        }

        // después de capturar los datos, hacemos un postproceso
        $data['status']               = ($data['status'] == 'HABILITADO');
        $data['tiene_acceso_sistema'] = ($data['tiene_acceso_sistema'] == 'CON CONTRASEÑA');
        // descartar direcciones vacías
        if ($data['fiscal']['direccion'] == '[--No Especificado--]' ||
            $data['fiscal']['municipio'] == '[--No Especificado--]') {
                unset($data['fiscal']);
        }
        if ($data['comercial']['direccion'] == '[--No Especificado--]' ||
            $data['comercial']['municipio'] == '[--No Especificado--]') {
                unset($data['comercial']);
        }

        // algunas fechas no están bien parseadas
        $data['rep_legales_updated']  = strptime($data['rep_legales_updated'], '(Datos recibidos de la SAT el: %d.%b.%Y %T ');
        $data['rep_legales_updated']  = 1900+$data['rep_legales_updated']['tm_year']
        . '-' . (1 + $data['rep_legales_updated']['tm_mon'])
        . '-' . ($data['rep_legales_updated']['tm_mday'])
        ;
        $data['url']                  = ($data['url']   != '[--No Especificado--]') ? $data['url'] : null;
        $data['email']                = ($data['email'] != '[--No Especificado--]') ? $data['email'] : null;

        $entity = new RepresentanteLegal();
        $entity->exchangeArray($data);

        $repLegales = $this->scrapRepresentantesLegales($id);
        foreach($repLegales as $newId) {
            $newRep = $this->scrap($newId);
            $entity->appendRepresentanteLegal($newRep);
        }

        $nombresComerciales = $this->scrapNombresComerciales($id);
        foreach ($nombresComerciales as $nombre) {
            $nombreComercial = new Entity\RepresentanteLegalNombreComercial();
            $nombreComercial->setNombre($nombre);
            $entity->appendNombreComercial($nombreComercial);
        }

        self::$scraped[$id] = $entity;
        return $entity;
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
        $página  = ScraperModel::getCachedUrl($url, "replegal-nombre-comercial-$id");
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
     * Retorna el ID de los representantes legales de una entidad
     *
     * Esta función puede recibir tanto un ID de un proveedor como de un representante legal
     *
     * @param int $id
     * @return int[]
     */
    public function scrapRepresentantesLegales($parentId)
    {
        $url       = 'http://guatecompras.gt/proveedores/consultaprrepleg.aspx?rqp=8&lprv=' . $parentId;
        $página    = ScraperModel::getCachedUrl($url, "proveedor-$parentId-reprlegales");
        $xpath     = '//*[@id="MasterGC_ContentBlockHolder_dgResultado"]//tr[not(@class="TablaTitulo")]/td[2]/a';
        $nodos     = $página->queryXpath($xpath);
        $elementos = [];
        foreach($nodos as $nodo) {
            $url         = parse_url($nodo->getAttribute('href'));
            parse_str($url['query'], $url);
            $id          = $url['lprv'];
            if ( ($parentId == $id) || in_array($id, $elementos)) continue;
            $elementos[] = (int) $id;
        }
        sort($elementos);
        return $elementos;
    }
}