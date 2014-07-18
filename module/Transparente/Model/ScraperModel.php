<?php
namespace Transparente\Model;

/**
 * Modelo del scraper
 *
 * @property ProveedorModel          $proveedorModel
 * @property RepresentanteLegalModel $representanteLegalModel
 */
class ScraperModel
{

    public function __construct(
            ProveedorModel          $proveedorModel,
            RepresentanteLegalModel $representanteLegalModel
    )
    {
        $this->proveedorModel          = $proveedorModel;
        $this->representanteLegalModel = $representanteLegalModel;
    }

    public static function fetchData($xpaths, \Zend\Dom\Query $dom)
    {
        $element = [];
        foreach($xpaths as $key => $path) {
            if (is_array($path)) {
                $element[$key] = self::fetchData($path, $dom);
            } else {
                if (!$path) {
                    $element[$key] = null;
                } else {
                    if ($path[0] == '/') {
                        $nodes = $dom->queryXpath($path);
                    } else {
                        $nodes = $dom->execute($path);
                    }
                    $nodesCount = count($nodes);
                    if ($nodesCount > 1) {
                        throw new \Exception("path '$path' not expecific enought, returned $nodesCount");
                    }
                    $node = $nodes->current();
                    if (!is_a($node , 'DOMElement')) {
                        $element[$key] = null;
                    } else {
                        $element[$key] = $node->nodeValue;
                    }
                }
            }
        }
        return $element;
    }

    /**
     * Returns a cached crawled URL
     *
     * @param string $url
     * @param string $method
     *
     * @return \Zend\Dom\Query
     */
    public static function getCachedUrl($url, $method='GET')
    {
        set_time_limit(0);
        ini_set('max_execution_time', 0);

        $key     = md5($method.$url.'0');
        $cache = \Zend\Cache\StorageFactory::factory([
            'adapter' => [
                'name'    => 'filesystem',
                'ttl'     => PHP_INT_MAX,
                'options' => ['cache_dir' => realpath('./data/cache')],
            ],
            'plugins' => array('serializer'),
        ]);
        if ($cache->hasItem($key)) {
            $dom = $cache->getItem($key);
        } else {
            $content       = file_get_contents($url);
            $content       = iconv('utf-8', 'iso-8859-1', $content);
            $dom           = new \Zend\Dom\Query($content);
            $cache->setItem($key, $dom);
        }
        return $dom;
    }

    /**
     * Conseguir todos los proveedores adjudicados del año en curso
     *
     * @return multitype:Ambigous <multitype:, number>
     *
     * @todo Solo los proveedores que no están en la DB son los que vamos a barrer
     */
    public function scrapProveedores()
    {
        $year            = date('Y');
        $proveedoresList = self::getCachedUrl('http://guatecompras.gt/proveedores/consultaProveeAdjLst.aspx?lper='.$year);
        $xpath           = "//a[starts-with(@href, './consultaDetProveeAdj.aspx')]";
        $proveedoresList = $proveedoresList->queryXpath($xpath);
        $proveedores     = [];
        foreach($proveedoresList as $nodo) {
            /* @var $proveedor DOMElement */
            // El link apunta a las adjudicaciones/projectos del proveedor, pero de aquí sacamos el ID del proveedor
            $url = parse_url($nodo->getAttribute('href'));
            parse_str($url['query'], $url);
            $idProveedor  = $url['lprv'];
            $data          = $this->proveedorModel->scrap($idProveedor);
            $data         += ['nombres_comerciales'    => $this->proveedorModel->scrapNombresComerciales($idProveedor)];
            $data         += ['representantes_legales' => $this->representanteLegalModel->scrapRepresentantesLegales($idProveedor)];
            $proveedores[] = $data;
        }
        return $proveedores;
    }
}