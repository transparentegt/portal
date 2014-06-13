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
 * @return \Zend\Dom\Query
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
echo '<!DOCTYPE html><head><meta charset="utf-8"></head><body><pre>';
// Conseguir todos los proveedores adjudicados del año en curso
$year            = date('Y');
$proveedoresList = getCachedUrl('http://guatecompras.gt/proveedores/consultaProveeAdjLst.aspx?lper='.$year);
// aquí es donde truena, el query de los links que su href empieze con ese path es lo que no se puede convertir a XPath
$xpath = "//a[starts-with(@href, './consultaDetProveeAdj.aspx')]";
$proveedoresList = $proveedoresList->queryXpath($xpath); // ^="./consultaDetProveeAdj.aspx
// @todo Este listado nos da los 50 primeros proveedores, necesitamos barrer más
foreach($proveedoresList as $proveedor) {
    /* @var $proveedor DOMElement */
    // El link apunta a las adjudicaciones/projectos del proveedor, pero de aquí sacamos el ID del proveedor
    $url = parse_url($proveedor->getAttribute('href'));
    parse_str($url['query'], $url);

    $proveedor = ['id' => $url['lprv']];

    // ahora podemos construir la URL para barrer los datos del proveedor
    // @todo Solo los proveedores que no están en la DB son los que vamos a barrer
    $url = "http://guatecompras.gt/proveedores/consultaDetProvee.aspx?rqp=8&lprv={$proveedor['id']}";
    $páginaDelProveedor = getCachedUrl($url);

    /**
     * Que valores vamos a buscar via xpath en la página del proveedor
     *
     * Usamos de nombre los campos de la base de datos para después solo volcar el arreglo con los resultados directo a
     * la DB. El nombre comercial no lo leemos de aquí, mejor buscamos en la URL que barre todos los nombres.
     *
     * @var array
     *
     * @todo barrer los nombres comerciales en su URL específica
     */
    $xpath              = [
        'nombre'               => '//*[@id="MasterGC_ContentBlockHolder_lblNombreProv"]',
        'nit'                  => '//*[@id="MasterGC_ContentBlockHolder_lblNIT"]',
        'status'               => '//*[@id="MasterGC_ContentBlockHolder_lblHabilitado"]',
        'tiene_acceso_sistema' => '//*[@id="MasterGC_ContentBlockHolder_lblContraSnl"]',
    ];
    foreach($xpath as $key => $path) {
        $proveedor[$key] = $páginaDelProveedor->queryXpath($path)->current()->nodeValue;
    }
    // después de capturar los datos, hacemos un postproceso
    $proveedor['status']               = ($proveedor['status'] == 'HABILITADO');
    // @todo getCachedUrl no está retornando el HTML como UTF-8
    $proveedor['tiene_acceso_sistema'] = ($proveedor['tiene_acceso_sistema'] == 'CON CONTRASEÃA');

    echo '<pre><strong>DEBUG::</strong> '.__FILE__.' +'.__LINE__."\n"; var_dump($proveedor); die();
}
