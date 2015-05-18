<?php
namespace Api\V1\Rest\PartidoPolitico;

class PartidoPoliticoEntity
{
    /**
     *
     * @var \Transparente\Model\Entity\PartidoPolitico
     */
    private  $entity;

    public function __construct(\Transparente\Model\Entity\PartidoPolitico $entity)
    {
        $this->entity = $entity;
    }

    public function getArrayCopy()
    {
        return [
                'id'        => $this->entity->getId(),
                'nombre'    => $this->entity->getNombre(),
                'iniciales' => $this->entity->getIniciales(),

        ];
    }
}
