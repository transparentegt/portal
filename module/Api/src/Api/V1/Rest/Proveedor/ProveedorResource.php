<?php
namespace Api\V1\Rest\Proveedor;

use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

class ProveedorResource extends AbstractResourceListener
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
     * Fetch a resource
     *
     * @param  string          $nit
     * @return ProveedorEntity
     */
    public function fetch($nit)
    {
        $nit = str_replace('-', '', $nit);
        $proveedorModel = $this->serviceManager->get('Transparente\Model\ProveedorModel');
        /* @var $proveedorModel \Transparente\Model\ProveedorModel */
        $proveedor      = $proveedorModel->findOneBy(['nit' => $nit]);
        if (!$proveedor) return false;
        return new ProveedorEntity($proveedor);
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = array())
    {
        return new ApiProblem(405, 'The GET method has not been defined for collections');
    }

}
