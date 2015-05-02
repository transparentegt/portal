<?php
namespace Transparente\Controller;

use Transparente\Model\DomicilioModel;
use Transparente\Model\ProveedorModel;
use Transparente\Model\RepresentanteLegalModel;
use Transparente\Model\Entity\EmpleadoMunicipal;

use Zend\Mvc\Controller\AbstractActionController;

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
     * Lee los empleados municipales del archivo data/xls2import/empleados_municipales.csv
     *
     * @return void
     */
    private function scrapEmpleadosMunicipales()
    {
        $domicilioModel       = $this->getServiceLocator()->get('Transparente\Model\DomicilioModel');
        /* @var $domicilioModel DomicilioModel */
        $partidoPolíticoModel = $this->getServiceLocator()->get('Transparente\Model\PartidoPoliticoModel');
        /* @var $partidoPolítico PartidoPoliticoModel */
        $db = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        /* @var $db Doctrine\ORM\EntityManager */

        $path  = realpath(__DIR__.'/../../../');
        $path .= '/data/xls2import/';
        $path .= 'empleados_municipales.csv';
        if (($handle = fopen($path, 'r')) === FALSE) {
            throw new \Exception("No se pudo leer el CSV '$path' para importar los empleados municipales");
        }
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if ($data[3] == 'VACANTE') continue;
            $municipio       = $domicilioModel->detectarMunicipio($data[0], $data[1], 'nombre');
            $partidoPolítico = $partidoPolíticoModel->findByIniciales($data[3])[0];
            if (!$partidoPolítico) {
                echo '<pre><strong>DEBUG::</strong> '.__FILE__.' +'.__LINE__."\n"; var_dump($data);
                throw new \Exception("No se encontró el partido político {$data[3]}");
            }

            $data = [
                'nombre1'   => $data[5],
                'nombre2'   => $data[6],
                'apellido1' => $data[7],
                'apellido2' => $data[8],
                'apellido3' => $data[9],
                'cargo'     => $data[2],
            ];

            $empleadoMunicipal = new EmpleadoMunicipal();
            $empleadoMunicipal->exchangeArray($data);
            $empleadoMunicipal->setPartidoPolitico($partidoPolítico);
            $empleadoMunicipal->setMunicipio($municipio);
            $db->persist($empleadoMunicipal);
        }
        fclose($handle);
        $db->flush();
        $db->clear();
    }

    private function scrapProveedores()
    {
        $proveedorModel = $this->getServiceLocator()->get('Transparente\Model\ProveedorModel');
        /* @var $proveedorModel ProveedorModel */
        $domicilioModel = $this->getServiceLocator()->get('Transparente\Model\DomicilioModel');
        /* @var $domicilioModel DomicilioModel */
        $proveedores = $proveedorModel->scrapList();
        foreach ($proveedores as $idProveedor) {
            $data      = $proveedorModel->scrap($idProveedor);
            $proveedor = new \Transparente\Model\Entity\Proveedor();
            $proveedor->exchangeArray($data);

            if (!empty($data['fiscal'])) {
                $domicilio = new \Transparente\Model\Entity\Domicilio();
                $domicilio->exchangeArray($data['fiscal']);
                try {
                    $domicilio = $domicilioModel->createFromScrappedData($data['fiscal']);
                    if ($domicilio) {
                        $proveedor->setDomicilioFiscal($domicilio);
                    }
                } catch (\Exception $e) {
                    echo '<pre><strong>DEBUG::</strong> '.__FILE__.' +'.__LINE__."\n"; var_dump($e->getMessage(), $data); die();
                }
            }

            if (!empty($data['comercial'])) {
                $domicilio = new \Transparente\Model\Entity\Domicilio();
                $domicilio->exchangeArray($data['comercial']);
                try {
                    $domicilio = $domicilioModel->createFromScrappedData($data['comercial']);
                    if ($domicilio) {
                        $proveedor->setDomicilioComercial($domicilio);
                    }
                } catch (\Exception $e) {
                    echo '<pre><strong>DEBUG::</strong> '.__FILE__.' +'.__LINE__."\n"; var_dump($e->getMessage(), $data); die();
                }
            }
            $proveedorModel->save($proveedor);
            $this->scrapProyectosAdjudicados($proveedor);
            $this->scrapRepresentantesLegales($proveedor);
            $this->scrapProveedorNombresComerciales($proveedor);
       }
    }

    private function scrapProveedorNombresComerciales($proveedor)
    {
        $proveedorModel = $this->getServiceLocator()->get('Transparente\Model\ProveedorModel');
        $nombresComerciales = $proveedorModel->scrapNombresComerciales($proveedor->getId());
        foreach ($nombresComerciales as $nombre) {
            $nombreComercial = new \Transparente\Model\Entity\ProveedorNombreComercial();
            $nombreComercial->setNombre($nombre);
            $proveedor->appendNombreComercial($nombreComercial);
        }
        $proveedorModel->save($proveedor);
    }

    private function scrapProyectosAdjudicados($proveedor)
    {
        $proyectoModel  = $this->getServiceLocator()->get('Transparente\Model\ProyectoModel');
        /* @var $protectoModel \Transparente\Model\ProyectoModel */
        $proyectosList = $proyectoModel->scrapList($proveedor);
        foreach ($proyectosList as $id) {
            if ($proyectoModel->find($id)) continue;
            $proyecto = $proyectoModel->scrap($id);
            $proyectoModel->save($proyecto);
        }
    }

    /**
     * Lee los representantes legales de todos los proveedores
     */
    private function scrapRepresentantesLegales($proveedor)
    {
        $proveedorModel = $this->getServiceLocator()->get('Transparente\Model\ProveedorModel');
        /* @var $proveedorModel ProveedorModel */
        $repModel       = $this->getServiceLocator()->get('Transparente\Model\RepresentanteLegalModel');
        /* @var $repModel RepresentanteLegalModel */
        $repList = $repModel->scrapRepresentantesLegales($proveedor->getId());
        $guardar = false; // no se actualiza la DB si no se agregaron representantes legales
        foreach ($repList as $id) {
            $repLegal = $repModel->scrap($id);
            if (!$repLegal) continue; // no tiene representante legal
            $proveedor->appendRepresentanteLegal($repLegal);
            $guardar = true;
        }
        if ($guardar) {
            $proveedorModel->save($proveedor);
        }
    }

    /**
     * Iniciando el scraper
     *
     * @todo preguntar en el CLI si se quiere hacer cada paso
     * @todo reiniciar la DB desde PHP y no desde el bash
     */
    public function indexAction()
    {
        $request = $this->getRequest();
        if (!$request instanceof \Zend\Console\Request){
            throw new \RuntimeException('Scraper solo puede ser corrido desde linea de comando.');
        }
        ini_set('memory_limit', -1);

        // la lectura de los empleados municipales son datos locales
        $this->scrapEmpleadosMunicipales();
        // empezamos la barrida de Guatecompras buscando los proveedores
        $this->scrapProveedores();
    }

}
