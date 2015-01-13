<?php
namespace Api\V1\Rest\EmpleadoMunicipal;

use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

class EmpleadoMunicipalResource extends AbstractResourceListener
{
    /**
     *
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    public function __construct(\Zend\ServiceManager\ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = array())
    {
        $Model     = $this->serviceManager->get('Transparente\Model\EmpleadoMunicipalModel');
        /* @var $proveedorModel \Transparente\Model\EmpleadoMunicipalModel */
        $paginator = $Model->getPaginator($params);
        /* @var $proveedor \Zend\Paginator\Paginator */
        return new EmpleadoMunicipalCollection($paginator->getAdapter());
    }
}
