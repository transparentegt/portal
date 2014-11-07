<?php
namespace Api\V1\Rest\Proveedor;

use Zend\Paginator\Paginator;

class ProveedorCollection extends Paginator
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
                // $elements = new ProveedorEntity($entity);
                $elements[] = [
                    'id'     => $entity->getId(),
                    'nit'    => $entity->getNit(),
                    'nombre' => $entity->getNombre(),
                ];
            }
            $elements = new \ArrayIterator($elements);
            return $elements;
        } catch (\Exception $e) {
            throw new \Exception\RuntimeException('Error producing an iterator', null, $e);
        }
    }
}
