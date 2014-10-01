<?php
namespace Transparente\Model;

use Doctrine\ORM\EntityRepository;
use Transparente\Model\Entity\AbstractDoctrineEntity;

abstract class AbstractModel extends EntityRepository
{

    /**
     * Guardar una entidad
     *
     * @param AbstractDoctrineEntity $entity
     */
    public function save(AbstractDoctrineEntity $entity)
    {
        $em = $this->getEntityManager();
        $em->persist($entity);
    }
}