<?php
namespace Transparente\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class EmpleadoMunicipalController extends AbstractActionController
{
    /**
     * Listar empleados municipales
     */
    public function indexAction()
    {
        return new ViewModel();
    }

}