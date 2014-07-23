<?php
namespace Transparente\Model;

use Doctrine\ORM\EntityRepository;
use Transparente\Model\Entity\Proveedor;

class ProveedorModel extends EntityRepository
{
    private function humanizarNombreDeEmpresa($nombre)
    {
        $nombre  = str_replace('SOCIEDAD ANONIMA', 'S.A.', $nombre);
        $nombres = preg_split('/[\s,]+/', mb_strtolower($nombre, 'UTF-8'));
        foreach ($nombres as $key => $nombre) {
            if (preg_match('/\./', $nombre)) {
                $nombres[$key] = strtoupper($nombre);
            } else {
                $nombres[$key] = ucfirst($nombre);
            }
        }
        $nombre = implode(' ', $nombres);
        return $nombre;
    }

    public function findAll()
    {
        return $this->findBy($criteria = [], $orderBy = ['nombre' => 'ASC']);
    }

    public function findByNoDomicilioFiscal()
    {
        $dql = 'SELECT Proveedor
                FROM Transparente\Model\Entity\Proveedor Proveedor
                WHERE Proveedor.domicilio_fiscal IS NULL
                ORDER BY Proveedor.nombre
                ';
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();

    }

    public function save(Proveedor $entity)
    {
        $em = $this->getEntityManager();
        $em->persist($entity);
    }

    /**
     * Lee todos los datos del proveedor según su ID
     *
     * @param int $id
     * @return array
     */
    public function scrap($id)
    {
        $url               = "http://guatecompras.gt/proveedores/consultaDetProvee.aspx?rqp=8&lprv={$id}";
        $páginaDelProveedor = ScraperModel::getCachedUrl($url);

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
                'direccion'    => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioFiscal2"]//tr[3]//td[2]',
                'telefonos'    => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioFiscal2"]//tr[4]//td[2]',
                'fax'          => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioFiscal2"]//tr[5]//td[2]',
            ],
            'domicilio_comercial'     => [
                'updated'      => null,
                'departamento' => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioComercial2"]//tr[3]//td[2]',
                'municipio'    => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioComercial2"]//tr[4]//td[2]',
                'direccion'    => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioComercial2"]//tr[5]//td[2]',
                'telefonos'    => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioComercial2"]//tr[6]//td[2]',
                'fax'          => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioComercial2"]//tr[7]//td[2]',
            ],
            'url'                 => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioComercial2"]//tr[1]//td[2]',
            'email'               => '//*[@id="MasterGC_ContentBlockHolder_pnl_domicilioComercial2"]//tr[2]//td[2]',
            'rep_legales_updated' => '//*[@id="MasterGC_ContentBlockHolder_divRepresentantesLegales"]//span/span',
        ];

        $proveedor = ['id' => $id] + ScraperModel::fetchData($xpaths, $páginaDelProveedor);

        // después de capturar los datos, hacemos un postproceso

        $proveedor['nombre']               = $this->humanizarNombreDeEmpresa($proveedor['nombre']);
        $proveedor['status']               = ($proveedor['status'] == 'HABILITADO');
        $proveedor['tiene_acceso_sistema'] = ($proveedor['tiene_acceso_sistema'] == 'CON CONTRASEÑA');
        // descartar direcciones vacías
        if ($proveedor['domicilio_fiscal']['direccion'] == '[--No Especificado--]' ||
            $proveedor['domicilio_fiscal']['municipio'] == '[--No Especificado--]') {
            unset($proveedor['domicilio_fiscal']);
        }
        if ($proveedor['domicilio_comercial']['direccion'] == '[--No Especificado--]' ||
            $proveedor['domicilio_comercial']['municipio'] == '[--No Especificado--]') {
            unset($proveedor['domicilio_comercial']);
        }

        // algunas fechas no están bien parseadas
        $proveedor['rep_legales_updated']  = strptime($proveedor['rep_legales_updated'], '(Datos recibidos de la SAT el: %d.%b.%Y %T ');
        $proveedor['rep_legales_updated']  = 1900+$proveedor['rep_legales_updated']['tm_year']
                                            . '-' . (1 + $proveedor['rep_legales_updated']['tm_mon'])
                                            . '-' . ($proveedor['rep_legales_updated']['tm_mday'])
                                            ;
        $proveedor['url']                  = ($proveedor['url']   != '[--No Especificado--]') ? $proveedor['url'] : null;
        $proveedor['email']                = ($proveedor['email'] != '[--No Especificado--]') ? $proveedor['email'] : null;
        return $proveedor;
    }

    /**
     * Conseguir todos los proveedores adjudicados del año en curso
     *
     * @return int[]
     *
     * @todo   No retorna datos de la página 3
     * @todo   Correr el paginado hasta que no retorne resultados
     */
    public function scrapList()
    {
        $year        = date('Y');
        $proveedores = [];
        $postVarsKey = [
            '_body:MasterGC$ContentBlockHolder$ScriptManager1' => 'MasterGC$ContentBlockHolder$UpdatePanel1|MasterGC$ContentBlockHolder$dgResultado$ctl54$ctl',
            '__EVENTTARGET'                                    => 'MasterGC$ContentBlockHolder$dgResultado$ctl54$ctl',
            '__EVENTARGUMENT'                                  => '',
            '__VIEWSTATE'                                      => '/wEPDwUKMTI4MzUzOTE3NA8WAh4CbFkC3g8WAmYPZBYCAgMPZBYCAgEPZBYCAgUPZBYCAgUPZBYCZg9kFgQCAw8WAh4EVGV4dAXNAjx0YWJsZSBjbGFzcz0iVGl0dWxvUGFnMSIgY2VsbFNwYWNpbmc9IjAiIGNlbGxQYWRkaW5nPSIyIiBhbGlnbj0iY2VudGVyIj48dHI+PHRkPjx0YWJsZSBjbGFzcz0iVGl0dWxvUGFnMiIgY2VsbFNwYWNpbmc9IjAiIGNlbGxQYWRkaW5nPSIyIj48dHI+PHRkIGNsYXNzPSJUaXR1bG9QYWczIiBhbGlnbj0iY2VudGVyIj48dGFibGUgY2xhc3M9IlRhYmxhRm9ybTMiIGNlbGxTcGFjaW5nPSIzIiBjZWxsUGFkZGluZz0iNCI+PHRyIGNsYXNzPSJFdGlxdWV0YUZvcm0yIj48dGQ+QcOxbzogMjAxNDwvdGQ+PC90cj48L3RhYmxlPjwvdGQ+PC90cj48L3RhYmxlPjwvdGQ+PC90cj48L3RhYmxlPmQCBw88KwALAQAPFgweC18hSXRlbUNvdW50AjIeCERhdGFLZXlzFgAeCVBhZ2VDb3VudAJNHhVfIURhdGFTb3VyY2VJdGVtQ291bnQC7B0eEFZpcnR1YWxJdGVtQ291bnQC7B0eEEN1cnJlbnRQYWdlSW5kZXgCAmRkZCE8gn3egStUa/LTQibKxHuPya2w',
            '__EVENTVALIDATION'                                => '/wEdAA3AcNkZJZYBmgLzXKMKQAHCDgb8Uag+idZmhp4z8foPgz4xN15UhY4K7pA9ni2czGCFp+0LzW2X25e7x6qJGAGNdTQnfVZ2Bpjxj7ZAwLTUHggMop+g+rIcjfLnqU7sIEd1r49BNud9Gzhdq5Du6Cuaivj/J0Sb6VUF9yYCq0O32nVzQBnAbvzxCHDPy/dQNW4JRFkop3STShyOPuu+QjyFyEKGLUzsAW/S22pN4CQ1k/PmspiPnyFdAbsK7K0ZtyIv/uu03tEXAoLdp793x+CRLD0M5v5yDc5Uyh02d+27XEUbbAI=',
            '__ASYNCPOST'                                      => 'true'
        ];
        for ($i = 1; $i <= 5; $i++) {
            $page     =  sprintf("%02d", $i);
            $postVars = $postVarsKey;
            $postVars['_body:MasterGC$ContentBlockHolder$ScriptManager1'] .= $page;
            $postVars['__EVENTTARGET']                                    .= $page;
            $html            = ScraperModel::getCachedUrl('http://guatecompras.gt/proveedores/consultaProveeAdjLst.aspx?lper='.$year, 'AJAX.NET', $postVars);
            $xpath           = "//a[starts-with(@href, './consultaDetProveeAdj.aspx')]";
            $proveedoresList = $html->queryXpath($xpath);
            echo 'Encontrados '.count($proveedoresList)." de 50\n";

            foreach ($proveedoresList as $nodo) {
                /* @var $proveedor DOMElement */
                // El link apunta a las adjudicaciones/projectos del proveedor, pero de aquí sacamos el ID del proveedor
                $url           = parse_url($nodo->getAttribute('href'));
                parse_str($url['query'], $url);
                $idProveedor   = (int) $url['lprv'];
                if (in_array($idProveedor, $proveedores)) {
                    echo '<pre><strong>DEBUG::</strong> '.__FILE__.' +'.__LINE__."\n"; var_dump('No se pudo leer la página '.$i, $postVars); die();

                }
                $proveedores[] = $idProveedor;
            }
        }
        return $proveedores;
    }

    /**
     * Obtiene los nombres comerciales de los proveedores
     *
     * @param int $id
     * @return array
     */
    public function scrapNombresComerciales($id)
    {
        $página  = ScraperModel::getCachedUrl('http://guatecompras.gt/proveedores/consultaProveeNomCom.aspx?rqp=8&lprv='.$id);
        $xpath   = '//*[@id="MasterGC_ContentBlockHolder_dgResultado"]//tr[not(@class="TablaTitulo")]/td[2]';
        $nodos   = $página->queryXpath($xpath);
        $nombres = [];
        foreach ($nodos as $nodo) {
            $nombre = $this->humanizarNombreDeEmpresa($nodo->nodeValue);
            if (in_array($nombre, $nombres)) continue;
            $nombres[] = $nombre;
        }
        sort($nombres);
        return $nombres;
    }
}