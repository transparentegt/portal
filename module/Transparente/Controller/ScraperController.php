<?php
namespace Transparente\Controller;

use Transparente\Model\DomicilioModel;
use Transparente\Model\ProveedorModel;
use Transparente\Model\RepresentanteLegalModel;
use Transparente\Model\ScraperModel;
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
        $repModel    = $this->getServiceLocator()->get('Transparente\Model\RepresentanteLegalModel');
        /* @var $repModel RepresentanteLegalModel */
        $domicilioModel = $this->getServiceLocator()->get('Transparente\Model\DomicilioModel');
        /* @var $domicilioModel DomicilioModel */

        $scraper     = new ScraperModel($proveedorModel, $repModel);
        $proveedores = $scraper->scrapProveedores();
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

            foreach ($data['nombres_comerciales'] as $nombre) {
                $nombreComercial = new \Transparente\Model\Entity\ProveedorNombreComercial();
                $nombreComercial->setNombre($nombre);
                $proveedor->appendNombreComercial($nombreComercial);
            }

            foreach ($data['representantes_legales'] as $idRep) {
                /* @var $domicilioModel DomicilioModel */
                $repLegal = $repModel->scrap($idRep);
                $proveedor->appendRepresentanteLegal($repLegal);
            }
            // echo '<pre><strong>DEBUG::</strong> '.__FILE__.' +'.__LINE__."\n"; Doctrine\Common\Util\Debug::dump($proveedor); die();
            // echo '<pre><strong>DEBUG::</strong> '.__FILE__.' +'.__LINE__."\n"; var_dump($data); die();

            $proveedorModel->save($proveedor);
       }
        $db = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        /* @var $db Doctrine\ORM\EntityManager */
        $db->flush();
        return new ViewModel(compact('proveedores'));
    }
}