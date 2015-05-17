<?php
namespace Transparente\Controller;

use Transparente\Model\DomicilioModel;
use Transparente\Model\ProveedorModel;
use Transparente\Model\RepresentanteLegalModel;
use Transparente\Model\Entity\EmpleadoMunicipal;

use Zend\Mvc\Controller\AbstractActionController;
use Transparente\Model\Entity\Exception\RepresentanteLegalException;

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
            $data     += ['nombres_comerciales'    => $proveedorModel->scrapNombresComerciales($idProveedor)];
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
            foreach ($data['nombres_comerciales'] as $nombre) {
                $nombreComercial = new \Transparente\Model\Entity\ProveedorNombreComercial();
                $nombreComercial->setNombre($nombre);
                $proveedor->appendNombreComercial($nombreComercial);
            }
            $proveedorModel->save($proveedor);
       }
    }

    /**
     * Lee los proyectos de todos los proveedores
     */
    private function scrapProyectosAdjudicados()
    {
        $proyectoModel  = $this->getServiceLocator()->get('Transparente\Model\ProyectoModel');
        /* @var $protectoModel ProyectoModel */
        $proveedorModel = $this->getServiceLocator()->get('Transparente\Model\ProveedorModel');
        /* @var $proveedorModel ProveedorModel */
        $proveedores    = $proveedorModel->findPendientesDeScrapearProyectos();
        foreach($proveedores as $proveedor) {
            $proyectosList = $proyectoModel->scrapList($proveedor);
            foreach ($proyectosList as $id) {
                if ($proyectoModel->find($id)) continue;
                $proyecto = $proyectoModel->scrap($id);
                $proyectoModel->save($proyecto);
            }
        }
    }

    /**
     * Lee los representantes legales de todos los proveedores
     */
    private function scrapRepresentantesLegales()
    {
        $proveedorModel = $this->getServiceLocator()->get('Transparente\Model\ProveedorModel');
        /* @var $proveedorModel ProveedorModel */
        $repModel       = $this->getServiceLocator()->get('Transparente\Model\RepresentanteLegalModel');
        /* @var $repModel RepresentanteLegalModel */
        $proveedores    = $proveedorModel->findAll([], ['id' => 'ASC']); // PendientesDeScrapearRepresentantesLegales();
        foreach($proveedores as $proveedor) {
            $repList = $repModel->scrapRepresentantesLegales($proveedor->getId());
            foreach ($repList as $id) {
                try {
                    $repLegal = $repModel->scrap($id);
                } catch (RepresentanteLegalException $e) {
                    echo "\n Proveedor #$id incompleto, continuemos\n";
                } catch (\Exception $e) {
                    throw new \Exception("No se pudo construir el representante legal #$id, para el proveedor #{$proveedor->getId()}", $id, $e);
                }
                $proveedor->appendRepresentanteLegal($repLegal);
            }
            $proveedorModel->update($proveedor);
        }
    }

    /**
     * Iniciando el scraper
     *
     * Primero leemos todos los proveedores pues hay pagos y representantes legales que necesitan tener ya insertados
     * a los proveedores, para no asociarlos a nulos.
     *
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
        // luego la barrida de los representantes legales de los proveedores
        $this->scrapRepresentantesLegales();
        // Ahora que ya tenemos los proveedores en la base de datos, ya podemos importar los proyectos
        $this->scrapProyectosAdjudicados();
    }

}
