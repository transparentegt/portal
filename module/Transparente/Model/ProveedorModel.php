<?php
namespace Transparente\Model;

use Doctrine\ORM\EntityRepository;

class ProveedorModel extends EntityRepository
{

    public function getAll()
    {
        $dql = 'SELECT p FROM \Transparente\Model\Entity\Proveedor p';
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();

    }

    public function save(\Transparente\Model\Entity\Proveedor $entity)
    {
        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();
    }

}