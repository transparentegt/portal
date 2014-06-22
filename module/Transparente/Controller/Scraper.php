<?php
namespace Transparente\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Transparente\Model\Proveedor;

/**
 * Scraper
 *
 * Tiempo aprox para leer solo los proveedores, 00:01:20
 *
 * @property Transparente\Model\ProveedoresTable $proveedoresTable
 *
 * @todo Convertirlo en un CLI
 */
class Scraper extends AbstractActionController
{
    /**
     * @var Transparente\Model\ProveedoresTable
     */
    private $proveedoresTable;

    private function getProveedoresTable()
    {
        if (!$this->proveedoresTable) {
            $sm = $this->getServiceLocator();
            $this->proveedoresTable = $sm->get('Transparente\Model\ProveedoresTable');
        }
        return $this->proveedoresTable;
    }

    /**
     * Iniciando el scraper
     */
    public function indexAction()
    {
        $scraper = new \Transparente\Model\Scraper();
        $proveedores = $scraper->scrapProveedores();
        foreach ($proveedores as $data) {
            $proveedor = new Proveedor();
            $proveedor->exchangeArray($data);
            $success = $this->getProveedoresTable()->save($proveedor);
            if (!$success) {
                throw new \Exeption("No se pudo grabar el proveedor '{$proveedor['id']}'.");
            }
        }

        return new ViewModel(compact('proveedores'));
    }
}