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
 * @todo Convertirlo en un modelo
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

    private function fetchData($xpaths, \Zend\Dom\Query $dom)
    {
        $element = [];
        foreach($xpaths as $key => $path) {
            if (is_array($path)) {
                $element[$key] = $this->fetchData($path, $dom);
            } else {
                if (!$path) {
                    $element[$key] = null;
                } else {
                    $node = $dom->queryXpath($path)->current();
                    if (!is_a($node , 'DOMElement')) {
                        $element[$key] = null;
                    } else {
                        $element[$key] = $dom->queryXpath($path)->current()->nodeValue;
                    }
                }
            }
        }
        return $element;
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
         * @todo Los xpath absolutos no los está encontrando
        */
        $xpaths = [
            'nombre'               => '//*[@id="MasterGC_ContentBlockHolder_lblNombreProv"]',
            'nit'                  => '//*[@id="MasterGC_ContentBlockHolder_lblNIT"]',
            'status'               => '//*[@id="MasterGC_ContentBlockHolder_lblHabilitado"]',
            'tiene_acceso_sistema' => '//*[@id="MasterGC_ContentBlockHolder_lblContraSnl"]',
            'domicilio_fiscal'     => [
                'updated'      => '/html/body/form/table[2]/tbody/tr/td/div[2]/div[1]/div/table/tbody/tr/td[2]/span/span',
                'departamento' => '/html/body/form/table[2]/tbody/tr/td/div[2]/div[2]/div/table/tbody/tr[1]/td[2]',
                'municipio'    => '/html/body/form/table[2]/tbody/tr/td/div[2]/div[2]/div/table/tbody/tr[2]/td[2]',
                'dirección'    => '/html/body/form/table[2]/tbody/tr/td/div[2]/div[2]/div/table/tbody/tr[3]/td[2]',
                'teléfonos'    => '/html/body/form/table[2]/tbody/tr/td/div[2]/div[2]/div/table/tbody/tr[4]/td[2]',
                'fax'          => '/html/body/form/table[2]/tbody/tr/td/div[2]/div[2]/div/table/tbody/tr[5]/td[2]',
            ],
            'domicilio_comercial'     => [
                'updated'      => null,
                'departamento' => '/html/body/form/table[2]/tbody/tr/td/div[3]/div[2]/div/table/tbody/tr[3]/td[2]',
                'municipio'    => '/html/body/form/table[2]/tbody/tr/td/div[3]/div[2]/div/table/tbody/tr[4]/td[2]',
                'dirección'    => '/html/body/form/table[2]/tbody/tr/td/div[3]/div[2]/div/table/tbody/tr[5]/td[2]',
                'teléfonos'    => '/html/body/form/table[2]/tbody/tr/td/div[3]/div[2]/div/table/tbody/tr[6]/td[2]',
                'fax'          => '/html/body/form/table[2]/tbody/tr/td/div[3]/div[2]/div/table/tbody/tr[7]/td[2]',
            ],
            'url'                 => '/html/body/form/table[2]/tbody/tr/td/div[3]/div[2]/div/table/tbody/tr[1]/td[2]',
            'email'               => '/html/body/form/table[2]/tbody/tr/td/div[3]/div[2]/div/table/tbody/tr[2]/td[2]',
            'rep_legales_updated' => '/html/body/form/table[2]/tbody/tr/td/div[4]/div[1]/div/table/tbody/tr/td[2]/span/span',
        ];

        $proveedor = $this->fetchData($xpaths, $páginaDelProveedor);
        echo '<pre><strong>DEBUG::</strong> '.__FILE__.' +'.__LINE__."\n"; var_dump($proveedor); die();


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