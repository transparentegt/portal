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
        $página = ScraperModel::getCachedUrl($url);

        $xpaths = [
            'categoría'   => '//*[@id="MasterGC_ContentBlockHolder_txtCategoria"]',
            'descripción' => '//*[@id="MasterGC_ContentBlockHolder_txtTitulo"]',
            'modalidad'   => '//*[@id="MasterGC_ContentBlockHolder_txtModalidad"]',
            'tipo'        => '//*[@id="MasterGC_ContentBlockHolder_txtTipo"]',
            'entidad'     => '//*[@id="MasterGC_ContentBlockHolder_txtEntidad"]',
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
        $pagerKeys   = [
            '_body:MasterGC$ContentBlockHolder$ScriptManager1' => 'MasterGC$ContentBlockHolder$UpdatePanel1|MasterGC$ContentBlockHolder$dgResultado$ctl55$ctl',
            '__EVENTTARGET'                                    => 'MasterGC$ContentBlockHolder$dgResultado$ctl55$ctl',
        ];
        $ids  = [];
        $page = 0;
        $year = date('Y');
        do {
            $page++;
            $html = ScraperModel::getCachedUrl(
                "http://guatecompras.gt/proveedores/consultaDetProveeAdj.aspx?rqp=5&lprv={$proveedor->getId()}&iTipo=1&lper=$year",
                ScraperModel::PAGE_MODE_PAGER,
                $pagerKeys,
                "proyectos-adjudicados-to-{$proveedor->getId()}-list-page-$page"
            );
            $xpath       = "//a[starts-with(@href, '../Concursos/consultaDetalleCon.aspx')]";
            $list        = $html->queryXpath($xpath);
            $encontrados = count($list);
            foreach ($list as $nodo) {
                /* @var $proveedor DOMElement */
                $url  = parse_url($nodo->getAttribute('href'));
                parse_str($url['query'], $url);
                $id   = (int) $url['nog'];
                if (!in_array($id, $ids)) {
                    $ids[]         = $id;
                }
            }
        } while($page <= $pagerKeys['totalPages']);
        sort($ids);
        return $ids;
    }

}