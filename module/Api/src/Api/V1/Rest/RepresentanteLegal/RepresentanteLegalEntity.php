<?php
namespace Api\V1\Rest\RepresentanteLegal;

class RepresentanteLegalEntity
{
    /**
     *
     * @var \Transparente\Model\Entity\Proveedor
     */
    private  $entity;

    public function __construct(\Transparente\Model\Entity\RepresentanteLegal $entity)
    {
        $this->entity = $entity;
    }

    public function getArrayCopy()
    {
        return $this->entity->getArrayCopy();
    }
}
