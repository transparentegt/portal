<?php
namespace Transparente\Model;

use Transparente\Model\Entity\Proveedor;
use Transparente\Model\Entity\Proyecto;
use Transparente\Model\Entity\Pago;

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
     * @todo ¿Qué hacer con los proveedores extranjeros?
     */
    public function scrap($id)
    {
        $url    = "http://guatecompras.gt/Concursos/consultaDetalleCon.aspx?nog={$id}&o=0";
        $página = ScraperModel::getCachedUrl($url, "proyecto-$id");
        // en orde de como los vemos en la página de GTC
        $xpaths = [
            // le pusimos "nombre" para estandarizar que toda entidad tenga un nombre, pero en GTC dice "descripción"
            'categoría'                   => '//*[@id="MasterGC_ContentBlockHolder_txtCategoria"]',
            'entidad_compradora'          => '//*[@id="MasterGC_ContentBlockHolder_txtEntidad"]/a',
            'entidad_compradora_tipo'     => '//*[@id="MasterGC_ContentBlockHolder_txtTipoEntidad"]',
            'entidad_compradora_unidad'   => '//*[@id="MasterGC_ContentBlockHolder_txtUE"]/a',
            'fecha_cierre_ofertas'        => '//*[@id="MasterGC_ContentBlockHolder_txtFechacierreRecep"]',
            'fecha_finalización'          => '//*[@id="MasterGC_ContentBlockHolder_txtFechaFinalización"]',
            'fecha_presentación_ofertas'  => '//*[@id="MasterGC_ContentBlockHolder_txtFechaPresentacion"]',
            'fecha_publicación'           => '//*[@id="MasterGC_ContentBlockHolder_txtFechaPub"]',
            'modalidad'                   => '//*[@id="MasterGC_ContentBlockHolder_txtModalidad"]',
            'nombre'                      => '//*[@id="MasterGC_ContentBlockHolder_txtTitulo"]',
            'status'                      => '//*[@id="MasterGC_ContentBlockHolder_txtEstatus"]',
            'tipo'                        => '//*[@id="MasterGC_ContentBlockHolder_txtTipo"]',
            'tipo_recepción_oferta'       => '//*[@id="MasterGC_ContentBlockHolder_txtRecepcionOferta"]',
        ];

        $data     = ['id' => $id,] + ScraperModel::fetchData($xpaths, $página);
        $proyecto = new Proyecto();
        $proyecto->exchangeArray($data);

        $pagosNodes = '//*[@id="MasterGC_ContentBlockHolder_dgProveedores"]//tr[starts-with(@class, "TablaFilaMix")]';
        $pagosNodes = $página->queryXpath($pagosNodes);
        foreach($pagosNodes as $node) {
            $proveedor = null;
            $monto     = iterator_to_array($node->childNodes);
            $monto     = $monto[3]->nodeValue;
            $monto     = (float) trim(str_replace(['Q',','], null, $monto));

            $subnodos = iterator_to_array($node->getElementsByTagName('a'));
            // si no se detectó el proveedor, es un país extranjero
            if ($subnodos) {
                $proveedor = parse_url($subnodos[0]->getAttribute('href'));
                parse_str($proveedor['query'], $proveedor);
                $proveedor = (int) $proveedor['lprv'];
                $proveedor = $this->getEntityManager()->getRepository('Transparente\Model\Entity\Proveedor')->find($proveedor);
            }

            $pago = new Pago();
            $pago->exchangeArray([
                'monto'     => $monto,
                'proyecto'  => $proyecto,
                'proveedor' => $proveedor,
            ]);
        }
        return $proyecto;
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
            echo "\nLeyendo cache:    $key";
            $ids = ScraperModel::getCache()->getItem($key);
        } else {
            $pagerKeys   = [
                '_body:MasterGC$ContentBlockHolder$ScriptManager1' => 'MasterGC$ContentBlockHolder$UpdatePanel1|MasterGC$ContentBlockHolder$dgResultado$ctl55$ctl',
                '__EVENTTARGET'                                    => 'MasterGC$ContentBlockHolder$dgResultado$ctl55$ctl',
            ];
            $ids   = [];
            $page  = 0;
            $start = "http://guatecompras.gt/proveedores/consultaDetProveeAdj.aspx?rqp=5&lprv={$proveedor->getId()}&iTipo=1";
            do {
                $page++;
                $html  = ScraperModel::getCachedUrl($start, "proveedor-{$proveedor->getId()}-proyectos-$page", ScraperModel::PAGE_MODE_PAGER, $pagerKeys);
                if (!$html) {
                    break;
                }
                $xpath = "//a[starts-with(@href, '../Concursos/consultaDetalleCon.aspx')]";
                $list  = $html->queryXpath($xpath);
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
