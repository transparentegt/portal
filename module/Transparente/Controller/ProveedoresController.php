<?php
namespace Transparente\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine;

class ProveedoresController extends AbstractActionController
{

    /**
     * Listar Proveedores
     *
     * @todo paginar
     * @todo ordenar por columna seleccionada
     */
    public function indexAction()
    {
        $proveedoresModel = $this->getServiceLocator()->get('Transparente\Model\ProveedorModel');
        $entities         = $proveedoresModel->findAll();
        return new ViewModel(compact('entities'));
    }

    /**
     * Ver los detalles de un proveedor
     */
    public function detallesAction()
    {
        $id               = $this->params('id');
        $proveedoresModel = $this->getServiceLocator()->get('Transparente\Model\ProveedorModel');
        $entity           = $proveedoresModel->find($id);
        return new ViewModel(compact('entity'));
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
        $entities         = $proveedoresModel->findByNoDomicilioFiscal();
        return new ViewModel(compact('entities'));
    }

}