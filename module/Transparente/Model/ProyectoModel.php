<?php
namespace Transparente\Model;

use Transparente\Model\Entity\Proveedor;
use Transparente\Model\Entity\Proyecto;

class ProyectoModel extends AbstractModel
{
    /**
     * Lee todos los datos del proveedor según su ID
     *
     * @param  int      $id
     * @return Proyecto
     *
     * @todo scrapear entidad compradora
     * @todo scrapear unidad compradora
     */
    public function scrap($id, $proveedorId)
    {
        $url    = "http://guatecompras.gt/Concursos/consultaDetalleCon.aspx?nog={$id}&o=10&rqp=5&lprv={$proveedorId}&iTipo=1&lper=2014";
        $página = ScraperModel::getCachedUrl($url, "proyecto-$id");
        // en orde de como los vemos en la página de GTC
        $xpaths = [
            // le pusimos "nombre" para estandarizar que toda entidad tenga un nombre, pero en GTC dice "descripción"
            'nombre'                      => '//*[@id="MasterGC_ContentBlockHolder_txtTitulo"]',
            'categoría'                   => '//*[@id="MasterGC_ContentBlockHolder_txtCategoria"]',
            'modalidad'                   => '//*[@id="MasterGC_ContentBlockHolder_txtModalidad"]',
            'tipo'                        => '//*[@id="MasterGC_ContentBlockHolder_txtTipo"]',
            'comprador'                   => '//*[@id="MasterGC_ContentBlockHolder_txtEntidad"]',
            'tipoComprador'               => '//*[@id="MasterGC_ContentBlockHolder_txtTipoEntidad"]',
            // 'unidadCompradora'            => '/html/body/form/table[2]/tbody/tr/td/div[3]/table/tbody/tr/td/table/tbody/tr/td/table/tbody/tr[9]/td[2]/span/a',
            'fechaPublicación'            => '//*[@id="MasterGC_ContentBlockHolder_txtFechaPub"]',
            'fechaPresentaciónOfertas'    => '//*[@id="MasterGC_ContentBlockHolder_txtFechaPresentacion"]',
            'fechaCierreRecepciónOfertas' => '//*[@id="MasterGC_ContentBlockHolder_txtFechacierreRecep"]',
            'tipoRecepciónOfertas'        => '//*[@id="MasterGC_ContentBlockHolder_txtRecepcionOferta"]',
            'status'                      => '//*[@id="MasterGC_ContentBlockHolder_txtEstatus"]',
        ];

        $data   = [
            'id'           => $id,
            'proveedor_id' => $proveedorId,
        ] + ScraperModel::fetchData($xpaths, $página);
        $entity = new Proyecto();
        $entity->exchangeArray($data);
        return $entity;
    }

    /**
     * Conseguir el listado de los IDs de los proyectos adjudicados del proveedor
     *
     * @return int[]
     *
     * @todo Quedan solo 2 $pagerKeys que debeoms de sacar de la primera página para quitar ese parámetro
     *
     * @todo Esto es TAN igual al ProveedorModel->scrapList() que deberíamos de moverlo a la superclase
     */
    public function scrapList(Proveedor $proveedor)
    {
        $key = "proveedor-{$proveedor->getId()}-proyectos-list";
        if (ScraperModel::getCache()->hasItem($key)) {
            echo "Leyendo cache: $key\n";
            $ids = ScraperModel::getCache()->getItem($key);
        } else {
            $pagerKeys   = [
                '_body:MasterGC$ContentBlockHolder$ScriptManager1' => 'MasterGC$ContentBlockHolder$UpdatePanel1|MasterGC$ContentBlockHolder$dgResultado$ctl55$ctl',
                '__EVENTTARGET'                                    => 'MasterGC$ContentBlockHolder$dgResultado$ctl55$ctl',
            ];
            $ids   = [];
            $page  = 0;
            $start = "http://guatecompras.gt/proveedores/consultaDetProveeAdj.aspx?rqp=5&lprv={$proveedor->getId()}&iTipo=1&lper=" . date('Y');
            do {
                $page++;
                $html  = ScraperModel::getCachedUrl($start, "proveedor-{$proveedor->getId()}-proyectos-$page", ScraperModel::PAGE_MODE_PAGER, $pagerKeys);
                if (!$html) {
                    break;
                }
                $xpath = "//a[starts-with(@href, '../Concursos/consultaDetalleCon.aspx')]";
                $list  = $html->queryXpath($xpath);
                $encontrados = count($list);
                foreach ($list as $nodo) {
                    /* @var $proveedor DOMElement */
                    $url  = parse_url($nodo->getAttribute('href'));
                    parse_str($url['query'], $url);
                    $id   = (int) $url['nog'];
                    if (!isset($ids[$id])) {
                        $ids[$id] = $id;
                    }
                }
            } while($page <= $pagerKeys['totalPages']);
            asort($ids);
            ScraperModel::getCache()->setItem($key, $ids);
        }
        return $ids;
    }

}