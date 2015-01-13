<?php
namespace Api\V1\Rest\PartidoPolitico;

use ZF\Rest\AbstractResourceListener;

class PartidoPoliticoResource extends AbstractResourceListener
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
     * @return PartidoPoliticoEntity
     */
    public function fetch($id)
    {
        $model = $this->serviceManager->get('Transparente\Model\PartidoPoliticoModel');
        /* @var $model \Transparente\Model\PartidoPoliticoModel */
        $entity = $model->find($id);
        if (!$entity) return false;
        return new PartidoPoliticoEntity($entity);
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return PartidoPoliticoCollection
     */
    public function fetchAll($params = [])
    {
        $model = $this->serviceManager->get('Transparente\Model\PartidoPoliticoModel');
        /* @var $model \Transparente\Model\PartidoPoliticoModel */
        $pager = $model->getPaginator($params);
        /* @var $proveedor \Zend\Paginator\Paginator */
        return new PartidoPoliticoCollection($pager->getAdapter());
    }
}
