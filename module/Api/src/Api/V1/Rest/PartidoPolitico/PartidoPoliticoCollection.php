<?php


namespace Api\V1\Rest\PartidoPolitico;

use Zend\Paginator\Paginator;

class PartidoPoliticoCollection extends Paginator
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
                /* @var $entity \Transparente\Model\Entity\PartidoPolitico */
                $elements[] = [
                    'id'           => $entity->getId(),
                    'nombre'       => $entity->getNombre(),
                    'iniciales'    => $entity->getIniciales(),
                ];
            }
            $elements = new \ArrayIterator($elements);
            return $elements;
        } catch (\Exception $e) {
            throw new \Exception\RuntimeException('Error producing an iterator', null, $e);
        }
    }
}
