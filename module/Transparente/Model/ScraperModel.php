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
    public static function getCachedUrl($url, $method='GET', $vars = null)
    {
        set_time_limit(0);
        ini_set('max_execution_time', 0);

        $key     = md5($method . $url . serialize($vars));
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
            switch ($method) {
                case 'GET':
                        $content       = file_get_contents($url);
                        $content       = iconv('utf-8', 'iso-8859-1', $content);
                        $dom           = new \Zend\Dom\Query($content);
                        $cache->setItem($key, $dom);
                    break;
                case 'POST':
                        $postdata = http_build_query($vars);
                        $opts     = ['http' => [
                                'method'  => 'POST',
                                'header'  => 'Content-type: application/x-www-form-urlencoded',
                                'content' => $postdata
                            ]
                        ];
                        $context  = stream_context_create($opts);
                        $result   = file_get_contents($url, false, $context, -1);
                        echo '<pre><strong>DEBUG::</strong> '.__FILE__.' +'.__LINE__."\n"; var_dump($result); die();
                    break;
                case 'AJAX.NET':
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_REFERER,        $url);
                    curl_setopt($ch, CURLOPT_USERAGENT,      'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.2 (KHTML, like Gecko) Chrome/5.0.342.3 Safari/533.2');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST,           1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS,     http_build_query($vars));
                    curl_setopt($ch, CURLOPT_HTTPHEADER,     ['Content-Type: application/x-www-form-urlencoded']);
                    $result = curl_exec ($ch);
                    $result = explode('|', $result)[7];
                    // echo '<pre><strong>DEBUG::</strong> '.__FILE__.' +'.__LINE__."\n"; var_dump($result); die();
                    $dom    = new \Zend\Dom\Query($result);
                    $cache->setItem($key, $dom);
                    break;
                default:
                    throw new \Exception("Cache type '$method' not defined");
            }
        }
        return $dom;
    }
}