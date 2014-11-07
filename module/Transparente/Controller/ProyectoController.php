<?php
namespace Transparente\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ProyectoController extends AbstractActionController
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
        $id     = $this->params('id');
        $model  = $this->getServiceLocator()->get('Transparente\Model\ProyectoModel');
        /* @var $empleadoMunicipalModel \Transparente\Model\ProyectoModel */
        $entity = $model->find($id);
        return new ViewModel(compact('entity'));
    }

}