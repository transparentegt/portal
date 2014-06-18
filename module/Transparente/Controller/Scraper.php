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
     * Iniciando el scraper
     */
    public function indexAction()
    {
        $scraper = new \Transparente\Model\Scraper();
        $proveedores = $scraper->scrapProveedores();
        return new ViewModel(compact('proveedores'));
    }
}