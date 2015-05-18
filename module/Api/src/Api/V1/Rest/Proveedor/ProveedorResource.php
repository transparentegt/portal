<?php
namespace Api\V1\Rest\Proveedor;

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
    public function fetch($id)
    {
        $proveedorModel = $this->serviceManager->get('Transparente\Model\ProveedorModel');
        /* @var $proveedorModel \Transparente\Model\ProveedorModel */
        if (strstr($id, '-')) { // si tiene un guión es un NIT
            $nit       = str_replace('-', '', $id);
            $proveedor = $proveedorModel->findOneBy(['nit' => $nit]);
        } else {
            $proveedor = $proveedorModel->find($id);
        }
        if (!$proveedor) return false;
        return new ProveedorEntity($proveedor);
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ProveedorCollection
     */
    public function fetchAll($params = [])
    {
        $proveedorModel = $this->serviceManager->get('Transparente\Model\ProveedorModel');
        /* @var $proveedorModel \Transparente\Model\ProveedorModel */
        $proveedor      = $proveedorModel->getPaginator($params);
        /* @var $proveedor \Zend\Paginator\Paginator */
        return new ProveedorCollection($proveedor->getAdapter());
    }
}