<?php
namespace Api\V1\Rest\Proyecto;

use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

class ProyectoResource extends AbstractResourceListener
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
     * @param  mixed $id
     * @return ApiProblem|mixed
     */
    public function fetch($id)
    {
        $db     = $this->serviceManager->get('Transparente\Model\ProyectoModel');
        /* @var $proveedorModel \Transparente\Model\ProyectoModel */
        $entity = $db->find($id);
        if (!$entity) return false;
        return new ProyectoEntity($entity);
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
