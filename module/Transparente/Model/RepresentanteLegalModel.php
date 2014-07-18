<?php
namespace Transparente\Model;

use Doctrine\ORM\EntityRepository;
use Transparente\Model\Entity\RepresentanteLegal;

class RepresentanteLegalModel extends EntityRepository
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
     * Partir el nombre para guardarlo ordenadamente.
     *
     * En GTC está en formato "$apellido1, $apellido2, $apellido3?, $nombre1, $nombre2"
     *
     * @param array $data
     */
    private function splitNombre(&$data)
    {
        $nombres           = explode(',', $data['nombre']);
        $nombre            = explode(',', $nombres[1]);
        $data['nombre1']   = $nombres[3];
        $data['nombre2']   = $nombres[4];
        $data['apellido1'] = $nombres[0];
        $data['apellido2'] = $nombres[1];
        unset($data['nombre']);
    }

    /**
     * Guardar un representante legal
     * @param RepresentanteLegal $entity
     *
     * @todo se usa? lo que guardamos es el proveedor y en cascada todo el representante legal
     */
    public function save(RepresentanteLegal $entity)
    {
        $em = $this->getEntityManager();
        $em->persist($entity);
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
        $página = ScraperModel::getCachedUrl($url);
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

        $data = ['id' => $id] + ScraperModel::fetchData($xpaths, $página);
        $this->splitNombre($data);
        // después de capturar los datos, hacemos un postproceso
        $data['status']               = ($data['status'] == 'HABILITADO');
        $data['tiene_acceso_sistema'] = ($data['tiene_acceso_sistema'] == 'CON CONTRASEÑA');
        // descartar direcciones vacías
        if ($data['domicilio_fiscal']['direccion'] == '[--No Especificado--]' ||
            $data['domicilio_fiscal']['municipio'] == '[--No Especificado--]') {
                unset($data['domicilio_fiscal']);
        }
        if ($data['domicilio_comercial']['direccion'] == '[--No Especificado--]' ||
            $data['domicilio_comercial']['municipio'] == '[--No Especificado--]') {
                unset($data['domicilio_comercial']);
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
        self::$scraped[$id] = $entity;
        return $entity;
    }

    /**
     * Retorna el ID de los representantes legales de una entidad
     *
     * Esta función puede recibir tanto un ID de un proveedor como de un representante legal
     *
     * @param int $id
     * @return int[]
     */
    public function scrapRepresentantesLegales($id)
    {
        $página    = ScraperModel::getCachedUrl('http://guatecompras.gt/proveedores/consultaprrepleg.aspx?rqp=8&lprv=' . $id);
        $xpath     = '//*[@id="MasterGC_ContentBlockHolder_dgResultado"]//tr[not(@class="TablaTitulo")]/td[2]/a';
        $nodos     = $página->queryXpath($xpath);
        $elementos = [];
        foreach($nodos as $nodo) {
            $url         = parse_url($nodo->getAttribute('href'));
            parse_str($url['query'], $url);
            $id          = $url['lprv'];
            if (in_array($id, $elementos)) continue;
            $elementos[] = (int) $id;
        }
        sort($elementos);
        return $elementos;
    }
}