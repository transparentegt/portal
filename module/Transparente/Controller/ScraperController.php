<?php
namespace Transparente\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Transparente\Model\Proveedor;
use Transparente\Model\Domicilio;

/**
 * ScraperController
 *
 * Tiempo aprox para leer solo los proveedores, 00:01:20
 *
 * @property Transparente\Model\ProveedoresTable $proveedoresTable
 *
 * @todo Convertirlo en un CLI
 */
class ScraperController extends AbstractActionController
{

    /**
     * @var Transparente\Model\DomiciliosTable
     */
    private $domiciliosTable;

    /**
     * @var Transparente\Model\ProveedoresTable
     */
    private $proveedoresTable;


    private function getDomiciliosTable()
    {
        if (!$this->proveedoresTable) {
            $sm = $this->getServiceLocator();
            $this->proveedoresTable = $sm->get('Transparente\Model\DomiciliosTable');
        }
        return $this->proveedoresTable;
    }

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
            $proveedor = $this->getProveedoresTable()->save($proveedor);
            $tainted   = false;
            if (!$proveedor) {
                throw new \Exception("No se pudo grabar el proveedor '{$proveedor['id']}'.");
            }
            if ($data['domicilio_fiscal']['direccion']) {
                $domicilio = new Domicilio();
                $domicilio->exchangeArray($data['domicilio_fiscal']);
                $domicilio = $this->getDomiciliosTable()->save($domicilio);

                echo '<pre><strong>DEBUG::</strong> '.__FILE__.' +'.__LINE__."\n"; var_dump($domicilio); die();

            }
            echo '<pre><strong>DEBUG::</strong> '.__FILE__.' +'.__LINE__."\n"; var_dump($proveedor); die();

        }

        return new ViewModel(compact('proveedores'));
    }
}