<?php
namespace Transparente\Model;

use Doctrine\ORM\EntityRepository;
use Transparente\Model\Entity\Proveedor;

class ProyectoModel extends EntityRepository
{
    /**
     * Lee todos los datos del proveedor según su ID
     *
     * @param int $id
     * @return array
     *
     * @todo scrapear entidad compradora
     * @todo scrapear unidad compradora
     */
    public function scrap($id)
    {
        $url    = "http://guatecompras.gt/Concursos/consultaDetalleCon.aspx?nog={$id}&o=10";
        $página = ScraperModel::getCachedUrl($url);

        $xpaths = [
            'categoría'   => '//*[@id="MasterGC_ContentBlockHolder_txtCategoria"]',
            'descripción' => '//*[@id="MasterGC_ContentBlockHolder_txtTitulo"]',
            'modalidad'   => '//*[@id="MasterGC_ContentBlockHolder_txtModalidad"]',
            'tipo'        => '//*[@id="MasterGC_ContentBlockHolder_txtTipo"]',
            'entidad'     => '//*[@id="MasterGC_ContentBlockHolder_txtEntidad"]',
        ];
        $proveedor = ['id' => $id] + ScraperModel::fetchData($xpaths, $página);

    }

    /**
     * Conseguir el listado de los IDs de los proyectos adjudicados del proveedor
     *
     * @return int[]
     *
     * @todo Detectar cuantas páginas hay que leer.
     *
     * @todo Reducir las variables de las llaves que son constantes entre diferentes paginadores, seteándolas en el
     *       scraper, y seteando solo las que son diferentes por paginador como parámetros.
     */
    public function scrapList(Proveedor $proveedor)
    {
        $pagerKeys   = [
                '_body:MasterGC$ContentBlockHolder$ScriptManager1' => 'MasterGC$ContentBlockHolder$UpdatePanel1|MasterGC$ContentBlockHolder$dgResultado$ctl55$ctl',
                '__EVENTTARGET'                                    => 'MasterGC$ContentBlockHolder$dgResultado$ctl55$ctl',
                '__VIEWSTATE'                                      => '/wEPDwULLTEyNTU2MzYzMDIPFgYeBk5PR0xuawMKHglsTm9Qcm92ZWUoKVlTeXN0ZW0uSW50NjQsIG1zY29ybGliLCBWZXJzaW9uPTQuMC4wLjAsIEN1bHR1cmU9bmV1dHJhbCwgUHVibGljS2V5VG9rZW49Yjc3YTVjNTYxOTM0ZTA4OQU0MTA0Mh4EbFBlcgLeDxYCZg9kFgICAw9kFgICAQ9kFgICBQ9kFggCBQ8PFgQeBFRleHQFHEouSS4gQ09IRU4sIFNPQ0lFREFEIEFOT05JTUEeC05hdmlnYXRlVXJsBSkuL2NvbnN1bHRhRGV0UHJvdmVlLmFzcHg/cnFwPTgmbHBydj00MTA0MmRkAgcPDxYCHwMFCDIyMzQ1Mzg4ZGQCCQ88KwALAQAPFggeCERhdGFLZXlzFgAeC18hSXRlbUNvdW50AgYeCVBhZ2VDb3VudAIBHhVfIURhdGFTb3VyY2VJdGVtQ291bnQCBmQWAmYPZBYOAgIPZBYGZg9kFgICAQ8PFgQfAwUEMjAxNB8EBT4uL2NvbnN1bHRhRGV0UHJvdmVlQWRqLmFzcHg/bHBlcj0yMDE0JnJxcD01JmxwcnY9NDEwNDImaVRpcG89MWRkAgEPDxYCHwMFBTEsMTUxZGQCAg8PFgIfAwUOMjg0LDE2MSw0MTguNzFkZAIDD2QWBmYPZBYCAgEPDxYEHwMFBDIwMTMfBAU+Li9jb25zdWx0YURldFByb3ZlZUFkai5hc3B4P2xwZXI9MjAxMyZycXA9NSZscHJ2PTQxMDQyJmlUaXBvPTFkZAIBDw8WAh8DBQUxLDI5NGRkAgIPDxYCHwMFDjM5Miw5ODMsMjMzLjc5ZGQCBA9kFgZmD2QWAgIBDw8WBB8DBQQyMDEyHwQFPi4vY29uc3VsdGFEZXRQcm92ZWVBZGouYXNweD9scGVyPTIwMTImcnFwPTUmbHBydj00MTA0MiZpVGlwbz0xZGQCAQ8PFgIfAwUFMSwxMjRkZAICDw8WAh8DBQ4xMDIsMDY5LDc3OS40OGRkAgUPZBYGZg9kFgICAQ8PFgQfAwUEMjAxMR8EBT4uL2NvbnN1bHRhRGV0UHJvdmVlQWRqLmFzcHg/bHBlcj0yMDExJnJxcD01JmxwcnY9NDEwNDImaVRpcG89MWRkAgEPDxYCHwMFBTEsMjcwZGQCAg8PFgIfAwUNODgsNjk1LDQzNy4yOGRkAgYPZBYGZg9kFgICAQ8PFgQfAwUEMjAxMB8EBT4uL2NvbnN1bHRhRGV0UHJvdmVlQWRqLmFzcHg/bHBlcj0yMDEwJnJxcD01JmxwcnY9NDEwNDImaVRpcG89MWRkAgEPDxYCHwMFAzc4MWRkAgIPDxYCHwMFDTYzLDAxMSw3NjEuOTVkZAIHD2QWBmYPZBYCAgEPDxYEHwMFBDIwMDkfBAU+Li9jb25zdWx0YURldFByb3ZlZUFkai5hc3B4P2xwZXI9MjAwOSZycXA9NSZscHJ2PTQxMDQyJmlUaXBvPTFkZAIBDw8WAh8DBQEyZGQCAg8PFgIfAwUKMTAyLDE3MS4zN2RkAggPZBYGZg9kFgICAQ8PFgIfBAU0Li9jb25zdWx0YURldFByb3ZlZUFkai5hc3B4P3JxcD01JmxwcnY9NDEwNDImaVRpcG89MWRkAgEPDxYCHwMFBTUsNjIyZGQCAg8PFgIfAwUOOTMxLDAyMyw4MDIuNThkZAIPD2QWAmYPZBYCAgMPPCsACwEADxYKHwYCMh8FFgAfBwIYHwgC/wgeEFZpcnR1YWxJdGVtQ291bnQC/whkZGTXZwWfd5SqQVbIBcAEWzKdESTypg==',
                '__EVENTVALIDATION'                                => '/wEdAA9LREma5igz5slDoSRLBf28HaHucUMA5SAPu3SveX9BBf/NaZcg5SPYk+JIxgvSWYbBqRb8VPt7LjDOQyE4WENPdZJkV49Xucpm0VV/6b0h7nYwq2aFZekVh79aucKgaI77JsE/uKmfwAjaUYgQDxoVGyD6qWrkPkk9DM+cN/KxX2l3rWGrVWE7Adc7dk6pHBjdDE6t2gIvmrKkUY1n2vEbNQMzDsD+bnTp+1tDsPoki5EeUQ9fBzpbpClP9t0n3tGqJJyahSZeiqw/zOKqyFyHpsECaRGyTs74kchOQllwMRTBwwICfRZm6NX7FDuG5TBuspGqjHqaVSN10HVtIEw6oupPPw==',
                ];
        $duplicados  = 0;
        $enPágina    = [];
        $encontrados = false;
        $ids         = [];
        $page        = 0;
        $year        = date('Y');
        do {
            $page++;
            $html = ScraperModel::getCachedUrl(
                "http://guatecompras.gt/proveedores/consultaDetProveeAdj.aspx?rqp=5&lprv={$proveedor->getId()}&iTipo=1&lper=$year",
                ScraperModel::PAGE_MODE_PAGER,
                $pagerKeys
            );
            $xpath       = "//a[starts-with(@href, '../Concursos/consultaDetalleCon.aspx')]";
            $list        = $html->queryXpath($xpath);
            $encontrados = count($list);
            foreach ($list as $nodo) {
                /* @var $proveedor DOMElement */
                $url  = parse_url($nodo->getAttribute('href'));
                parse_str($url['query'], $url);
                $id   = (int) $url['lprv'];
                if (!in_array($id, $ids)) {
                    $enPágina[$id] = $page;
                    $ids[]         = $id;
                } else {
                    $duplicados++;
                    $encontrados--;
                    $páginaOriginal = $enPágina[$id];
                    // echo "ERROR: Se encontró proveedor duplicado ($id)  en las páginas $páginaOriginal y $page\n";
                }
            }
        } while($encontrados > 0);
        $total = count($ids);
        echo "LOG: proveedores por leer: $total, proveedores duplicados: $duplicados\n";
        return $ids;
    }

}