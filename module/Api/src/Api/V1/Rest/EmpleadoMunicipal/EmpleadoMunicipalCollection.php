<?php
namespace Api\V1\Rest\EmpleadoMunicipal;

use Zend\Paginator\Paginator;

class EmpleadoMunicipalCollection extends Paginator
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
                /* @var $entity \Transparente\Model\Entity\EmpleadoMunicipal */
                $elements[] = [
                    'id'           => $entity->getId(),
                    /*
                    'nombre1'      => $entity->getNombre1(),
                    'nombre2'      => $entity->getNombre2(),
                    'apellido1'    => $entity->getApellido1(),
                    'apellido2'    => $entity->getApellido2(),
                    'apellido3'    => $entity->getApellido3(),
                    */
                    'nombre'       => $entity->getNombre(),
                    'cargo'        => $entity->getCargo(),
                    'municipio_id' => $entity->getMunicipio()->getId(),
                    'municipio'    => $entity->getMunicipio()->getNombre(),
                    'departamento' => $entity->getMunicipio()->getDepartamento()->getNombre(),
                ];
            }
            $elements = new \ArrayIterator($elements);
            return $elements;
        } catch (\Exception $e) {
            throw new \Exception\RuntimeException('Error producing an iterator', null, $e);
        }
    }
}
