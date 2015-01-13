<?php
namespace Api\V1\Rest\RepresentanteLegal;

use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

class RepresentanteLegalResource extends AbstractResourceListener
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
        $model = $this->serviceManager->get('Transparente\Model\RepresentanteLegalModel');
        /* @var $model \Transparente\Model\RepresentanteLegalModel */
        $entity = $model->find($id);

        if (!$entity) return false;
        return new RepresentanteLegalEntity($entity);
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = array())
    {
        $model = $this->serviceManager->get('Transparente\Model\RepresentanteLegalModel');
        /* @var $model \Transparente\Model\RepresentanteLegalModel */
        $paginator      = $model->getPaginator($params);
        /* @var $proveedor \Zend\Paginator\Paginator */
        return new RepresentanteLegalCollection($paginator->getAdapter());
    }
}
