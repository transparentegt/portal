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
    const BASE_URL        = 'http://transparente.gt';
    const PAGE_MODE_GET   = 'GET';
    const PAGE_MODE_POST  = 'POST';
    const PAGE_MODE_PAGER = 'PAGER';

    private static $cache;

    /**
     * Convierte las fechas de formato GTC dia.mesEnEspañol.año en un objeto DateTime
     * @param  string    $date
     * @return \DateTime
     */
    public static function fecha($date)
    {
        $meses = [
                [null, 'ene',   'feb',     'mar',   'abr',   'may',  'jun',   'jul',   'ago',    'sep',        'oct',     'nov',        'dic'],
                [null, 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'],
        ] ;
        $matches = false;
        preg_match('/(\d+)\.(\w+)\.(\d+)/', $date, $matches);
        if (!$matches) throw new \Exception("No se pudo detectar la fecha: '$date'");
        $día = $matches[1];
        $año = $matches[3];
        foreach ($meses as $nombres) {
            $mes = array_search($matches[2], $nombres);
            if ($mes) break;
        }
        if (!$mes) throw new \Exception("No se pudo detectar la fecha para el mes: '{$matches[2]}'");
        $fecha = "$año-$mes-$día";
        $fecha = new \DateTime($fecha);
        return $fecha;
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
                        try {
                            $nodes = $dom->queryXpath($path);
                        } catch (\Exception $e) {
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
     * Creates a cache singleton
     * @return \Zend\Cache\Storage\StorageInterface
     */
    public static function getCache()
    {
        if (!self::$cache) {
            self::$cache = \Zend\Cache\StorageFactory::factory([
                'adapter' => [
                    'name'    => 'filesystem',
                    'ttl'     => PHP_INT_MAX,
                    'options' => ['cache_dir' => realpath('./data/cache')],
                ],
                'plugins' => array('serializer'),
            ]);
        }
        return self::$cache;
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
        $timeStart  = $timeEnd = 0;
        self::profileTime($timeStart, $timeEnd);
        $downloaded = false;
        $key       .= "-$method";
        $cache      = self::getCache();
        if ($cache->hasItem($key)) {
            echo sprintf("\nLeyendo cache:    %-40s %-80s", $key, $url);
            $content = $cache->getItem($key);
        } else {
            echo sprintf("\nLeyendo original: %-40s %-80s", $key, $url);
            $downloaded = true;
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
                        $totalItems = $domQuery->execute($cssItems);
                        $totalItems = $totalItems[0]->textContent;
                        $totalItems = explode(' ',$totalItems)[4];
                        $totalItems = \NumberFormatter::create('en_US', \NumberFormatter::DECIMAL)->parse($totalItems);
                        if (!$totalItems) {
                            return false;
                        }
                        $totalPages = ceil($totalItems/50);

                        $vars['totalItems'] = $totalItems;
                        $vars['totalPages'] = $totalPages;

                        // definir llaves del paginador basados en la primera página
                        $node = 'input[name="__EVENTVALIDATION"]';
                        $node = $domQuery->execute($node);
                        if ($node) {
                            $vars['__EVENTVALIDATION'] = $node[0]->getAttribute('value');
                        }
                        $node = 'input[name="__VIEWSTATE"]';
                        $node = $domQuery->execute($node)[0];
                        if ($node) {
                            $vars['__VIEWSTATE'] = $node->getAttribute('value');
                        }
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
                    $response = curl_exec ($ch);
                    $response = substr($response, 6);
                    $content  = [];
                    do {
                        $response  = substr($response, 1);
                        $length    = sscanf($response, '%d')[0];
                        $response  = substr($response, strlen($length) +1);
                        $fieldName = strstr($response, '|', true);
                        $response  = substr($response, strlen($fieldName) +1);
                        $tmp       = strstr($response, '|', true);
                        if ($tmp) {
                            $fieldName = $tmp;
                            $response  = substr($response, strlen($tmp));
                        }
                        $response            = substr($response, 1);
                        $content[$fieldName] = substr($response, 0, $length);
                        $response            = substr($response, $length);
                    } while (strlen($response));

                    $vars['__VIEWSTATE']       = $content['__VIEWSTATE'];
                    $vars['__EVENTVALIDATION'] = $content['__EVENTVALIDATION'];
                    $content                   = array_shift($content);
                    break;
                default:
                    throw new \Exception("Cache type '$method' not defined");
            }
        }
        if (!$content) {
            throw new \Exception("No se pudo leer la URL $url");
        }
        if ($downloaded) {
            $cache->setItem($key, $content);
        }
        $dom = new \Zend\Dom\Query($content);
        echo sprintf("\tcache time: %s", self::profileTime($timeStart, $timeEnd));
        return $dom;
    }

    /**
     * Retorna el texto en modo nombre propio
     *
     * @param string $string
     * @return string
     */
    public static function nombresPropios($nombre)
    {
        $nombre  = str_replace(['"', '(', ')'], ['', ', ', ''], $nombre);
        $nombres = preg_split('/[\s,\.]+/', $nombre);
        $nombre  = '';
        foreach ($nombres as $n) {
            $n = trim($n);
            if (!$n) continue;
            $nombre .= ' ';
            // detectamos si tiene caracteres extraños
            if (preg_match('/[^a-zÑñÁÉÍÓÚáéíóúÜü]/i', $n)) {
                $nombre .= mb_convert_case(trim($n), MB_CASE_UPPER, 'UTF-8');
            } else {
                // en caso contrario es un nombre propio
                $nombre .= mb_convert_case(trim($n), MB_CASE_TITLE, 'UTF-8');
            }
        }
        $nombre = trim($nombre);
        return $nombre;
    }

    public static function profileTime(&$start, &$end = null, $decimals = 5)
    {
        $elapsed = 0;
        if (!$start) {
            $start = microtime(true);
        } else {
            $end     = microtime(true);
            $elapsed = number_format($end - $start, $decimals);
        }
        return $elapsed;
    }

}