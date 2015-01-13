<?php
namespace Api\V1\Rest\RepresentanteLegal;

use Zend\Paginator\Paginator;

class RepresentanteLegalCollection extends Paginator
{

    /**
     * Returns a foreach-compatible iterator.
     *
     * @throws Exception\RuntimeException
     * @return Traversable
     */
    public function getIterator()
    {
        try {
            $page     = $this->getCurrentItems();
            $elements = [];
            foreach ($page as $entity) {
                /* @var $entity \Transparente\Model\Entity\RepresentanteLegal */
                $data        = $entity->getArrayCopy();
                $proveedores = [];
                unset($data['nombres_comerciales']);
                unset($data['representantes_legales']);
                foreach($entity->getProveedores() as $proveedor) {
                    $proveedorData = $proveedor->getArrayCopy();
                    $proveedores[] = [
                        'id'     => $proveedorData['id'],
                        'nombre' => $proveedorData['nombre'],
                    ];
                }
                $data['proveedores'] = $proveedores;
                $elements[]          = $data;
            }

            $elements = new \ArrayIterator($elements);
            return $elements;
        } catch (\Exception $e) {
            throw new \Exception\RuntimeException('Error producing an iterator', null, $e);
        }
    }
}
