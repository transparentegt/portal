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
                        $result   = file_get_contents($url, false, $context, -1, 40000);
                        echo '<pre><strong>DEBUG::</strong> '.__FILE__.' +'.__LINE__."\n"; var_dump($result); die();
                    break;
                case 'REST':

                    //next example will insert new conversation
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $vars);
                    $curl_response = curl_exec($curl);
                    if ($curl_response === false) {
                        $info = curl_getinfo($curl);
                        curl_close($curl);
                        die('error occured during curl exec. Additioanl info: ' . var_export($info));
                    }
                    echo '<pre><strong>DEBUG::</strong> '.__FILE__.' +'.__LINE__."\n"; var_dump($curl_response, curl_getinfo($curl)); die();
                    curl_close($curl);


                    $decoded = json_decode($curl_response);
                    if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
                        die('error occured: ' . $decoded->response->errormessage);
                    }
                    echo 'response ok!';
                    var_export($decoded->response);

                    break;
            }
            if ($method == 'GET') {
            } elseif ($method == 'POST') {

            }
        }
        return $dom;
    }
}