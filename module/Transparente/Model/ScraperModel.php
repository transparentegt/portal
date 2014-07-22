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
}