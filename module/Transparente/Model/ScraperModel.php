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
                        try {
                            $nodes = $dom->queryXpath($path);
                        } catch (\Exception $e) {
                            echo '<pre><strong>DEBUG::</strong> '.__FILE__.' +'.__LINE__."\n"; var_dump($dom->getDocument()); die();

                            throw new \Exception("No se encontríó el path '$path'", null, $e);
                        }
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
     * @return Array Request
     */
    public static function getCachedUrl($url, $method='GET', $vars = null, $key = null)
    {
        $data = array('body' => '', '__VIEWSTATE' => '', '__EVENTVALIDATION' => '');
        if (!$key) {
            $key = md5($method . $url . serialize($vars));
        }
        $cache = \Zend\Cache\StorageFactory::factory([
            'adapter' => [
                'name'    => 'filesystem',
                'ttl'     => PHP_INT_MAX,
                'options' => ['cache_dir' => realpath('./data/cache')],
            ],
            'plugins' => array('serializer'),
        ]);
        if ($cache->hasItem($key)) {
            echo "Leyendo del cache:\t $url\n";
            $data = $cache->getItem($key);
        } else {
            echo "Leyendo del sitio original:\t $url\n";
            switch ($method) {
                case 'GET':
                        $request = null;
                        while(!$request) {
                            try {
                                $request = file_get_contents($url);
                            } catch (\Exception $e) {
                                echo "No se pudo leer la página: $url. Deteniendo el sistema 2 segundos\n";
                                sleep(2);
                            }
                        }
                        $data['body'] = iconv('utf-8', 'iso-8859-1', $request);
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
                        $data['body']   = file_get_contents($url, false, $context, -1);
                        //echo '<pre><strong>DEBUG::</strong> '.__FILE__.' +'.__LINE__."\n"; var_dump($data['content']); die();
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
                    $request = curl_exec ($ch);

                    $request = explode('|', $request);
                    $data['body']               = $request[7];
                    $data['__VIEWSTATE']        = $request[19];
                    $data['__EVENTVALIDATION']  = $request[23];
                    // echo '<pre><strong>DEBUG::</strong> '.__FILE__.' +'.__LINE__."\n"; var_dump($result); die();
                    break;
                default:
                    throw new \Exception("Cache type '$method' not defined");
            }
        }
       
        //echo '<pre><strong>DEBUG::</strong> '.__FILE__.' +'.__LINE__."\n"; var_dump($data); die();
        if (!$data['body']) {
            throw new \Exception("No se pudo leer la URL $url");
        }
        $cache->setItem($key, $data);
        return $data;
    }
}