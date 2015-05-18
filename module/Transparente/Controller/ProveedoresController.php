<?php
namespace Transparente\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ProveedoresController extends AbstractActionController
{
    /**
     * Listar Proveedores
     *
     * @todo ordenar por columna seleccionada
     */
    public function indexAction()
    {
        $proveedoresModel = $this->getServiceLocator()->get('Transparente\Model\ProveedorModel');
        $paginator        = $proveedoresModel->getPaginator();
        return new ViewModel(compact('paginator'));
    }

    /**
     * Ver los detalles de un proveedor
     */
    public function detallesAction()
    {
        $id                        = $this->params('id');
        $proveedoresModel          = $this->getServiceLocator()->get('Transparente\Model\ProveedorModel');
        $empleadosMunicipalesModel = $this->getServiceLocator()->get('Transparente\Model\EmpleadoMunicipalModel');
        /* @var $empleadoMunicipalModel \Transparente\Model\EmpleadoMunicipalModel */
        $entity                    = $proveedoresModel->find($id);
        $empleadosMunicipales      = $empleadosMunicipalesModel->findByRepresentantesLegalesDelProveedor($entity);
        return new ViewModel(compact('empleadosMunicipales', 'entity'));
    }

    /**
     * Listar proveedores sin dirección fiscal
     *
     * @todo paginar
     * @todo ordenar por nombre pre-seleccionado
     * @todo ordenar por columna seleccionada
     */
    public function noFiscalAction()
    {
        $proveedoresModel = $this->getServiceLocator()->get('Transparente\Model\ProveedorModel');
        $paginator        = $proveedoresModel->getPagerSinDomicilioFiscal();
        $total            = $proveedoresModel->findCount();
        return new ViewModel(compact('total', 'paginator'));
    }

}