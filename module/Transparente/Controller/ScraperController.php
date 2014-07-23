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
 */
class ScraperController extends AbstractActionController
{
    /**
     * Iniciando el scraper
     *
     * @todo correr el scraper desde CLI
     */
    public function indexAction()
    {
        $request = $this->getRequest();
        if (!$request instanceof \Zend\Console\Request){
            throw new \RuntimeException('Scraper solo puede ser corrido desde linea de comando.');
        }

        $proveedorModel = $this->getServiceLocator()->get('Transparente\Model\ProveedorModel');
        /* @var $proveedorModel ProveedorModel */
        $repModel    = $this->getServiceLocator()->get('Transparente\Model\RepresentanteLegalModel');
        /* @var $repModel RepresentanteLegalModel */
        $domicilioModel = $this->getServiceLocator()->get('Transparente\Model\DomicilioModel');
        /* @var $domicilioModel DomicilioModel */

        $scraper = new ScraperModel($proveedorModel, $repModel);
        $totales = [
            'proveedores' => 0,
            'domicilios'  => 0,
            'repLegales'  => 0,
        ];

        $proveedores = $proveedorModel->scrapList();
        foreach ($proveedores as $idProveedor) {
            $totales['proveedores']++;
            $data      = $proveedorModel->scrap($idProveedor);
            $data     += ['nombres_comerciales'    => $proveedorModel->scrapNombresComerciales($idProveedor)];
            $data     += ['representantes_legales' => $repModel->scrapRepresentantesLegales($idProveedor)];
            $proveedor = new \Transparente\Model\Entity\Proveedor();
            $proveedor->exchangeArray($data);

            if (!empty($data['domicilio_fiscal'])) {
                $totales['domicilios']++;
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
                $totales['domicilios']++;
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
                $totales['repLegales']++;
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
        return new ViewModel(compact('totales'));
    }
}