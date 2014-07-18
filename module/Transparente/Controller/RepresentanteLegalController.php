<?php
namespace Transparente\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine;

class RepresentanteLegalController extends AbstractActionController
{
    /**
     * Listar representantes legales
     *
     * @todo paginar
     * @todo ordenar por columna seleccionada
     */
    public function indexAction()
    {
        $modelo    = $this->getServiceLocator()->get('Transparente\Model\RepresentanteLegalModel');
        $entidades = $modelo->findAll();
        return new ViewModel(compact('entidades'));
    }

    /**
     * Ver los detalles de un representante legal
     */
    public function detallesAction()
    {
        $id          = $this->params('id');
        $entityModel = $this->getServiceLocator()->get('Transparente\Model\RepresentanteLegalModel');
        $entity      = $entityModel->find($id);
        return new ViewModel(compact('entity'));
    }

    public function multiProveedorAction()
    {
        $modelo    = $this->getServiceLocator()->get('Transparente\Model\RepresentanteLegalModel');
        $entidades = $modelo->findByMultiProveedor();
        return new ViewModel(compact('entidades'));
    }

    public function multiLevelAction()
    {
        $modelo    = $this->getServiceLocator()->get('Transparente\Model\RepresentanteLegalModel');
        $entidades = $modelo->findByMultiLevel();
        return new ViewModel(compact('entidades'));
    }
}