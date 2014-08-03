<?php
namespace Transparente\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class MunicipioController extends AbstractActionController
{

    /**
     * Ver los detalles de un proveedor
     */
    public function detallesAction()
    {
        $id             = $this->params('id');
        $municipioModel = $this->getServiceLocator()->get('Transparente\Model\MunicipioModel');
        $municipio      = $municipioModel->findOneById($id);

        $empleadosMunicipales = $municipio->getEmpleadosMunicipales();

        $representanteLelgalModel = $this->getServiceLocator()->get('Transparente\Model\RepresentanteLegalModel');
        $representantes           = [];
        foreach ($empleadosMunicipales as $empleado) {
            $representantes[$empleado->getId()] = $representanteLelgalModel->findByEmpleadoMunicipal($empleado);
        }
        return new ViewModel(compact('empleadosMunicipales', 'municipio', 'representantes'));
    }

}