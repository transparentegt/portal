<?php
require_once 'Zend/Loader/StandardAutoloader.php';
$zfloader = new Zend\Loader\StandardAutoloader(array('autoregister_zf' => true));
$zfloader->register();

use Zend\Dom\Query;



/**
 * Returns a cached crawled URL
 *
 * @param string $url
 * @param string $method
 * @return \Symfony\Component\DomCrawler\Crawler
 */
function getCachedUrl($url, $method='GET') {
    if (!isset($_SESSION['scraper.cache'])) {
        $_SESSION['scraper.cache'] = [];
    }
    $key = md5($method.$url);
    if (!isset($_SESSION['scraper.cache'][$key])) {
        $content = file_get_contents($url);
        $dom     = new Query($content, 'utf-8');
        $_SESSION['scraper.cache'][$key] = $dom;
    }
    $dom = $_SESSION['scraper.cache'][$key];
    return $dom;
}


session_start();
echo '<!DOCTYPE html><head><meta charset="utf-8"></head><body>';
// Conseguir todos los proveedores adjudicados del año en curso
$year            = date('Y');
$proveedoresList = getCachedUrl('http://guatecompras.gt/proveedores/consultaProveeAdjLst.aspx?lper='.$year);
// aquó es donde truena, el query de los links que su href empieze con ese path es lo que no se puede convertir a XPath
$proveedoresList = $proveedoresList->execute('a[href^="./consultaDetProveeAdj.aspx"]');
echo '<pre><strong>DEBUG::</strong> '.__FILE__.' +'.__LINE__."\n"; var_dump(count($proveedoresList)); die();