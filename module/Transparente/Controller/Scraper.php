<?php
namespace Transparente\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Scraper
 *
 * Tiempo aprox para leer solo los proveedores, 00:01:20
 *
 * @todo Convertirlo en un CLI
 */
class Scraper extends AbstractActionController
{
    /**
     * Returns a cached crawled URL
     *
     * @param string $url
     * @param string $method
     * @return \Zend\Dom\Query
     *
     * @todo Verificar el charset, según veo en la página de GTC, el encoding es UTF-8, pero al leerlo y escupirlo
     *       muestra caracteres extraños
     */
    private function getCachedUrl($url, $method='GET') {
        $session = new \Zend\Session\Container('Transparente\Scraper\cache');
        $charset =  'utf-8';
        $key     = md5($method.$url.$charset);
        if (!isset($session[$key])) {
            $content       = file_get_contents($url);
            $dom           = new \Zend\Dom\Query($content, $charset);
            $session[$key] = $dom;
        }
        $dom = $session[$key];
        return $dom;
    }

    /**
     * Lee todos los datos del proveedor según su ID
     *
     * @param int $id
     * @return array
     *
    * @todo getCachedUrl no está retornando el HTML como UTF-8 y no se puede leer bien si tiene contraseña o no
     */
    private function scrapProveedor($id)
    {
        // ahora podemos construir la URL para barrer los datos del proveedor
        // @todo Solo los proveedores que no están en la DB son los que vamos a barrer
        $url = "http://guatecompras.gt/proveedores/consultaDetProvee.aspx?rqp=8&lprv={$id}";
        $páginaDelProveedor = $this->getCachedUrl($url);

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
        $xpath = [
            'nombre'               => '//*[@id="MasterGC_ContentBlockHolder_lblNombreProv"]',
            'nit'                  => '//*[@id="MasterGC_ContentBlockHolder_lblNIT"]',
            'status'               => '//*[@id="MasterGC_ContentBlockHolder_lblHabilitado"]',
            'tiene_acceso_sistema' => '//*[@id="MasterGC_ContentBlockHolder_lblContraSnl"]',
        ];
        $proveedor = [];
        foreach($xpath as $key => $path) {
            $proveedor[$key] = $páginaDelProveedor->queryXpath($path)->current()->nodeValue;
        }
        // después de capturar los datos, hacemos un postproceso
        $proveedor['status']               = ($proveedor['status'] == 'HABILITADO');
        $proveedor['tiene_acceso_sistema'] = ($proveedor['tiene_acceso_sistema'] == 'CON CONTRASEÃA');
        return $proveedor;
    }

    /**
     * Iniciando el scraper
     */
    public function indexAction()
    {
        // Conseguir todos los proveedores adjudicados del año en curso
        $year            = date('Y');
        $proveedoresList = $this->getCachedUrl('http://guatecompras.gt/proveedores/consultaProveeAdjLst.aspx?lper='.$year);
        // aquí es donde truena, el query de los links que su href empieze con ese path es lo que no se puede convertir a XPath
        $xpath = "//a[starts-with(@href, './consultaDetProveeAdj.aspx')]";
        $proveedoresList = $proveedoresList->queryXpath($xpath); // ^="./consultaDetProveeAdj.aspx

        $proveedores = [];
        foreach($proveedoresList as $proveedor) {
            /* @var $proveedor DOMElement */
            // El link apunta a las adjudicaciones/projectos del proveedor, pero de aquí sacamos el ID del proveedor
            $url = parse_url($proveedor->getAttribute('href'));
            parse_str($url['query'], $url);
            $proveedores[] = $this->scrapProveedor($url['lprv']);
        }
        echo '<pre><strong>DEBUG::</strong> '.__FILE__.' +'.__LINE__."\n"; var_dump($proveedores); die();

        return new ViewModel();
    }
}