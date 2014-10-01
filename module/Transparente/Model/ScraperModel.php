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
    const PAGE_MODE_GET   = 'GET';
    const PAGE_MODE_POST  = 'POST';
    CONST PAGE_MODE_PAGER = 'PAGER';

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
     * Retorna el contenido de la página solicitada, y la guarda en cache por si la vuelven a pedir
     *
     * @param  string $url    La dirección a conseguir
     * @param  string $key    Nombre del cache
     * @param  string $method El método a usar para pedir la página, GET|POST|PAGER
     * @param  array  $vars   Si hay variables que pasar y retornar, se usa en PAGER
     * @throws \Exception
     * @return \Zend\Dom\Query
     */
    public static function getCachedUrl($url, $key, $method = self::PAGE_MODE_GET, &$vars = [])
    {
        $key  .= "-$method-".substr(md5(serialize($vars)), 0, 6);
        $cache = \Zend\Cache\StorageFactory::factory([
            'adapter' => [
                'name'    => 'filesystem',
                'ttl'     => PHP_INT_MAX,
                'options' => ['cache_dir' => realpath('./data/cache')],
            ],
            'plugins' => array('serializer'),
        ]);
        if ($cache->hasItem($key)) {
            echo "Leyendo cache:   \t$method\t$url\t\t$key\n";
            $content = $cache->getItem($key);
        } else {
            echo "Leyendo original:\t$method\t$url\t\t$key\n";
            switch ($method) {
                case self::PAGE_MODE_GET:
                    $content = null;
                    while(!$content) {
                        try {
                            $content = file_get_contents($url);
                        } catch (\Exception $e) {
                            echo "No se pudo leer la página: $url. Deteniendo el sistema 2 segundos\n";
                            sleep(2);
                        }
                    }
                    $content = iconv('utf-8', 'iso-8859-1', $content);
                    break;
                case self::PAGE_MODE_POST:
                    $postdata = http_build_query($vars);
                    $opts     = ['http' => [
                            'method'  => 'POST',
                            'header'  => 'Content-type: application/x-www-form-urlencoded',
                            'content' => $postdata
                        ]
                    ];
                    $context = stream_context_create($opts);
                    $content = file_get_contents($url, false, $context, -1);
                    break;
                case self::PAGE_MODE_PAGER:
                    if (!isset($vars['page'])) {
                        $vars['page']            = 1;
                        $vars['__ASYNCPOST']     = 'true';
                        $vars['__EVENTARGUMENT'] = '';
                        $domQuery                = ScraperModel::getCachedUrl($url, $key, self::PAGE_MODE_GET, $vars);

                        $cssItems   = "span#MasterGC_ContentBlockHolder_lblFilas";
                        $totalItems = explode(' ',$domQuery->execute($cssItems)[0]->textContent)[4];
                        $totalItems = \NumberFormatter::create('en_US', \NumberFormatter::DECIMAL)->parse($totalItems);
                        $totalPages = ceil($totalItems/50);

                        $vars['totalItems'] = $totalItems;
                        $vars['totalPages'] = $totalPages;

                        // definir llaves del paginador basados en la primera página
                        $cssEventValidation        = 'input[name="__EVENTVALIDATION"]';
                        $vars['__EVENTVALIDATION'] = $domQuery->execute($cssEventValidation)[0]->getAttribute('value');
                        $cssViewState              = 'input[name="__VIEWSTATE"]';
                        $vars['__VIEWSTATE']       = $domQuery->execute($cssViewState)[0]->getAttribute('value');

                        // la llamada recursiva nos devuelve el objeto ya listo para retornarlo
                        return $domQuery;
                    }
                    $vars['page']++;
                    $ctl = $vars['page'];
                    if ($vars['page'] > 11 && ($vars['page'] % 10 > 1)) {
                        $ctl = $vars['page'] % 10 + 1;
                    } elseif ($vars['page'] > 11 && ($vars['page'] % 10  == 0)) {
                        $ctl = 11;
                    } elseif ($vars['page'] > 11 && ($vars['page'] % 10  == 1)) {
                        $ctl = 12;
                    }
                    $ctl      = sprintf("%02d", $ctl);
                    $postVars = $vars;
                    unset($postVars['page']);
                    unset($postVars['ctl']);
                    $postVars['_body:MasterGC$ContentBlockHolder$ScriptManager1'] .= $ctl;
                    $postVars['__EVENTTARGET']                                    .= $ctl;

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_REFERER,        $url);
                    curl_setopt($ch, CURLOPT_USERAGENT,      'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.2 (KHTML, like Gecko) Chrome/5.0.342.3 Safari/533.2');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST,           1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS,     http_build_query($postVars));
                    curl_setopt($ch, CURLOPT_HTTPHEADER,     ['Content-Type: application/x-www-form-urlencoded']);
                    $request = curl_exec ($ch);
                    $request = explode('|', $request);

                    $vars['__VIEWSTATE']       = $request[19];
                    $vars['__EVENTVALIDATION'] = $request[23];
                    $content                   = $request[7];
                    break;
                default:
                    throw new \Exception("Cache type '$method' not defined");
            }
        }

        //echo '<pre><strong>DEBUG::</strong> '.__FILE__.' +'.__LINE__."\n"; var_dump($data); die();
        if (!$content) {
            throw new \Exception("No se pudo leer la URL $url");
        }
        $cache->setItem($key, $content);
        $dom = new \Zend\Dom\Query($content);
        return $dom;
    }

    /**
     * Retorna el texto en modo nombre propio
     *
     * @param string $string
     * @return string
     */
    public static function nombresPropios($string)
    {
        $string = mb_convert_case(trim($string), MB_CASE_TITLE, 'UTF-8');
        return $string;
    }

}