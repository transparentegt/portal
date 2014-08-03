<?php
namespace Transparente\Model;

use Doctrine\ORM\EntityRepository;
use Transparente\Model\Entity\Proveedor;

class ProyectoModel extends EntityRepository
{

    /**
     * Conseguir el listado de los IDs de los proyectos adjudicados del proveedor
     *
     * @return int[]
     *
     * @todo Detectar cuantas páginas hay que leer.
     *
     * @todo Reducir las variables de las llaves que son constantes entre diferentes paginadores, seteándolas en el
     *       scraper, y seteando solo las que son diferentes por paginador como parámetros.
     */
    public function scrapList(Proveedor $proveedor)
    {
        $year        = date('Y');
        $proveedores = [];
        $pagerKeys   = [
                '_body:MasterGC$ContentBlockHolder$ScriptManager1' => 'MasterGC$ContentBlockHolder$UpdatePanel1|MasterGC$ContentBlockHolder$dgResultado$ctl54$ctl',
                '__EVENTTARGET'                                    => 'MasterGC$ContentBlockHolder$dgResultado$ctl54$ctl',
                '__EVENTARGUMENT'                                  => '',
                '__VIEWSTATE'                                      => '/wEPDwUKMTI4MzUzOTE3NA8WAh4CbFkC3g8WAmYPZBYCAgMPZBYCAgEPZBYCAgUPZBYCAgUPZBYCZg9kFgQCAw8WAh4EVGV4dAXNAjx0YWJsZSBjbGFzcz0iVGl0dWxvUGFnMSIgY2VsbFNwYWNpbmc9IjAiIGNlbGxQYWRkaW5nPSIyIiBhbGlnbj0iY2VudGVyIj48dHI+PHRkPjx0YWJsZSBjbGFzcz0iVGl0dWxvUGFnMiIgY2VsbFNwYWNpbmc9IjAiIGNlbGxQYWRkaW5nPSIyIj48dHI+PHRkIGNsYXNzPSJUaXR1bG9QYWczIiBhbGlnbj0iY2VudGVyIj48dGFibGUgY2xhc3M9IlRhYmxhRm9ybTMiIGNlbGxTcGFjaW5nPSIzIiBjZWxsUGFkZGluZz0iNCI+PHRyIGNsYXNzPSJFdGlxdWV0YUZvcm0yIj48dGQ+QcOxbzogMjAxNDwvdGQ+PC90cj48L3RhYmxlPjwvdGQ+PC90cj48L3RhYmxlPjwvdGQ+PC90cj48L3RhYmxlPmQCBw88KwALAQAPFgoeC18hSXRlbUNvdW50AjIeCERhdGFLZXlzFgAeCVBhZ2VDb3VudAJNHhVfIURhdGFTb3VyY2VJdGVtQ291bnQC/h0eEFZpcnR1YWxJdGVtQ291bnQC/h1kZGTFGnAqD6UqwZ6veVDVd8I1rSzrhg==',
                '__EVENTVALIDATION'                                => '/wEdAA14XElF3qXk6b0iXGg7E00zDgb8Uag+idZmhp4z8foPgz4xN15UhY4K7pA9ni2czGB1NCd9VnYGmPGPtkDAtNQeEDIBsVJcI17AvX4wvuIJ5AgMop+g+rIcjfLnqU7sIEd1r49BNud9Gzhdq5Du6Cuaivj/J0Sb6VUF9yYCq0O32nVzQBnAbvzxCHDPy/dQNW4JRFkop3STShyOPuu+QjyFyEKGLUzsAW/S22pN4CQ1k/PmspiPnyFdAbsK7K0ZtyIv/uu03tEXAoLdp793x+CRlm7Yn37MSDqo7lpN9Z9v4u6Js8E=',
                '__ASYNCPOST'                                      => 'true'
                ];
        $proveedorEnPágina = [];
        $encontrados       = false;
        $duplicados        = 0;
        $page = 0;
        do {
            $page++;
            $html = ScraperModel::getCachedUrl(
                    "http://guatecompras.gt/proveedores/consultaDetProveeAdj.aspx?rqp=5&lprv={$proveedor->getId()}&iTipo=1&lper=$year",
                    ScraperModel::PAGE_MODE_PAGER,
                    $pagerKeys,
                    "proveedores-list-page-$page"
            );
            $xpath           = "//a[starts-with(@href, './consultaDetProveeAdj.aspx')]";
            $proveedoresList = $html->queryXpath($xpath);
            $encontrados     = count($proveedoresList);
            foreach ($proveedoresList as $nodo) {
                /* @var $proveedor DOMElement */
                // El link apunta a las adjudicaciones/projectos del proveedor, pero de aquí sacamos el ID del proveedor
                $url           = parse_url($nodo->getAttribute('href'));
                parse_str($url['query'], $url);
                $idProveedor   = (int) $url['lprv'];
                if (!in_array($idProveedor, $proveedores)) {
                    $proveedorEnPágina[$idProveedor] = $page;
                    $proveedores[]                   = $idProveedor;
                } else {
                    $duplicados++;
                    $encontrados--;
                    $páginaOriginal = $proveedorEnPágina[$idProveedor];
                    echo "ERROR: Se encontró proveedor duplicado ($idProveedor)  en las páginas $páginaOriginal y $page\n";
                }
                }
                // } while($encontrados > 0);
        } while ($page <= self::PAGE_MAX);
        $total = count($proveedores);
        echo "LOG: proveedores por leer: $total, proveedores duplicados: $duplicados\n";
        return $proveedores;
    }

}