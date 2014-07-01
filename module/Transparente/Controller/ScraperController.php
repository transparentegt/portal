<?php
namespace Transparente\Controller;

use Transparente\Model\ProveedorModel;
use Transparente\Model\DomicilioModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

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
     * Iniciando el scraper
     */
    public function indexAction()
    {
        $proveedorModel = $this->getServiceLocator()->get('Transparente\Model\ProveedorModel');
        /* @var $proveedorModel ProveedorModel */
        $domicilioModel = $this->getServiceLocator()->get('Transparente\Model\DomicilioModel');
        /* @var $domicilioModel DomicilioModel */

        $scraper        = new \Transparente\Model\Scraper();
        $proveedores    = $scraper->scrapProveedores();
        foreach ($proveedores as $data) {
            $proveedor = new \Transparente\Model\Entity\Proveedor();
            $proveedor->exchangeArray($data);

            if (!empty($data['domicilio_fiscal'])) {
                $domicilio = new \Transparente\Model\Entity\Domicilio();
                $domicilio->exchangeArray($data['domicilio_fiscal']);
                try {
                    $domicilio = $domicilioModel->createFromScrappedData($data['domicilio_fiscal']);
                    if ($domicilio) {
                        $proveedor->setDomicilioFiscal($domicilio);
                    }
                } catch (\Exception $e) {
                    echo '<pre><strong>DEBUG::</strong> '.__FILE__.' +'.__LINE__."\n"; var_dump($data); die();
                }
            }

            if (!empty($data['domicilio_comercial'])) {
                $domicilio = new \Transparente\Model\Entity\Domicilio();
                $domicilio->exchangeArray($data['domicilio_comercial']);
                try {
                    $domicilio = $domicilioModel->createFromScrappedData($data['domicilio_comercial']);
                    if ($domicilio) {
                        $proveedor->setDomicilioComercial($domicilio);
                    }
                } catch (\Exception $e) {
                    echo '<pre><strong>DEBUG::</strong> '.__FILE__.' +'.__LINE__."\n"; var_dump($data); die();
                }
            }

            try {
                $proveedorModel->save($proveedor);
            } catch (\Exception $e) {
                echo '<pre><strong>DEBUG::</strong> '.__FILE__.' +'.__LINE__."\n";
                \Doctrine\Common\Util\Debug::dump($proveedor);
                var_dump($data);
                die();
            }
        }
        return new ViewModel(compact('proveedores'));
    }
}