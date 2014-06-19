<?php
namespace Transparente\Model;

class Scraper
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
     * Obtiene los nombres comerciales de los proveedores
     *
     * @param int $id
     * @return array
     */
    public function scrapNombresComercialesDelProveedor($id)
    {
        $página  = $this->getCachedUrl('http://guatecompras.gt/proveedores/consultaProveeNomCom.aspx?rqp=8&lprv='.$id);
        $xpath   = '//*[@id="MasterGC_ContentBlockHolder_dgResultado"]//tr[not(@class="TablaTitulo")]/td[2]';
        $nodos   = $página->queryXpath($xpath);
        $nombres = [];
        foreach($nodos as $nodo) {
            if (in_array($nodo->nodeValue, $nombres)) continue;
            $nombres[] = $nodo->nodeValue;
        }
        sort($nombres);
        return $nombres;
    }

    /**
     * Lee todos los datos del proveedor según su ID
     *
     * @param int $id
     * @return array
     *
     * @todo getCachedUrl no está retornando el HTML como UTF-8 y no se puede leer bien si tiene contraseña o no
     */
    public function scrapProveedor($id)
    {
        $url               = "http://guatecompras.gt/proveedores/consultaDetProvee.aspx?rqp=8&lprv={$id}";
        $páginaDelProveedor = $this->getCachedUrl($url);

        /**
         * Que valores vamos a buscar via xpath en la página del proveedor
         *
         * Usamos de nombre los campos de la base de datos para después solo volcar el arreglo con los resultados directo a
         * la DB.
         *
         * @var array
         */
        $xpaths = [
            'nombre'               => '//*[@id="MasterGC_ContentBlockHolder_lblNombreProv"]',
            'nit'                  => '//*[@id="MasterGC_ContentBlockHolder_lblNIT"]',
            'status'               => '//*[@id="MasterGC_ContentBlockHolder_lblHabilitado"]',
            'tiene_acceso_sistema' => '//*[@id="MasterGC_ContentBlockHolder_lblContraSnl"]',
            'domicilio_fiscal'     => [
                'updated'      => 'div#MasterGC_ContentBlockHolder_divDomicilioFiscal span.AvisoGrande span.AvisoGrande',
                'departamento' => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioFiscal2"]//tr[1]//td[2]',
                'municipio'    => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioFiscal2"]//tr[2]//td[2]',
                'dirección'    => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioFiscal2"]//tr[3]//td[2]',
                'teléfonos'    => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioFiscal2"]//tr[4]//td[2]',
                'fax'          => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioFiscal2"]//tr[5]//td[2]',
            ],
            'domicilio_comercial'     => [
                'updated'      => null,
                'departamento' => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioComercial2"]//tr[3]//td[2]',
                'municipio'    => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioComercial2"]//tr[4]//td[2]',
                'dirección'    => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioComercial2"]//tr[5]//td[2]',
                'teléfonos'    => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioComercial2"]//tr[6]//td[2]',
                'fax'          => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioComercial2"]//tr[7]//td[2]',
            ],
            'url'                 => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioComercial2"]//tr[1]//td[2]',
            'email'               => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioComercial2"]//tr[2]//td[2]',
            'rep_legales_updated' => '//*[@id="MasterGC_ContentBlockHolder_divRepresentantesLegales"]//span/span',
        ];

        $proveedor = ['id' => $id] + $this->fetchData($xpaths, $páginaDelProveedor);

        // después de capturar los datos, hacemos un postproceso
        $proveedor['status']               = ($proveedor['status'] == 'HABILITADO');
        $proveedor['tiene_acceso_sistema'] = ($proveedor['tiene_acceso_sistema'] == 'CON CONTRASEÃA');
        // algunas fechas no están bien parseadas
        $proveedor['rep_legales_updated']  = strptime($proveedor['rep_legales_updated'], '(Datos recibidos de la SAT el: %d.%b.%Y %T ');
        $proveedor['rep_legales_updated']  = 1900+$proveedor['rep_legales_updated']['tm_year']
                                            . '-' . (1 + $proveedor['rep_legales_updated']['tm_mon'])
                                            . '-' . ($proveedor['rep_legales_updated']['tm_mday'])
                                            ;
        $proveedor['url']                  = ($proveedor['url'] != '[--No Especificado--]') ? $proveedor['url'] : null;
        $proveedor['email']                = ($proveedor['email'] != '[--No Especificado--]') ? $proveedor['email'] : null;
        return $proveedor;
    }

    /**
     * Conseguir todos los proveedores adjudicados del año en curso
     *
     * @return multitype:Ambigous <multitype:, number>
     *
     * @todo Solo los proveedores que no están en la DB son los que vamos a barrer
     */
    public function scrapProveedores()
    {
        $year            = date('Y');
        $proveedoresList = $this->getCachedUrl('http://guatecompras.gt/proveedores/consultaProveeAdjLst.aspx?lper='.$year);
        $xpath           = "//a[starts-with(@href, './consultaDetProveeAdj.aspx')]";
        $proveedoresList = $proveedoresList->queryXpath($xpath);

        $proveedores = [];
        foreach($proveedoresList as $proveedor) {
            /* @var $proveedor DOMElement */
            // El link apunta a las adjudicaciones/projectos del proveedor, pero de aquí sacamos el ID del proveedor
            $url = parse_url($proveedor->getAttribute('href'));
            parse_str($url['query'], $url);
            $idProveedor   = $url['lprv'];
            $proveedor     = $this->scrapProveedor($idProveedor);
            $proveedor    += ['nombres_comerciales'    => $this->scrapNombresComercialesDelProveedor($idProveedor)];
            $proveedor    += ['representantes_legales' => $this->scrapRepresentantesLegales($idProveedor)];
            $proveedores[] = $proveedor;
        }
        return $proveedores;
    }

    public function scrapRepresentantesLegales($id)
    {
        $página    = $this->getCachedUrl('http://guatecompras.gt/proveedores/consultaprrepleg.aspx?rqp=8&lprv=' . $id);
        $xpath     = '//*[@id="MasterGC_ContentBlockHolder_dgResultado"]//tr[not(@class="TablaTitulo")]/td[2]';
        $nodos     = $página->queryXpath($xpath);
        $elementos = [];
        foreach($nodos as $nodo) {
            if (in_array($nodo->nodeValue, $elementos)) continue;
            $elementos[] = trim($nodo->nodeValue);
        }
        sort($elementos);
        return $elementos;
    }

}