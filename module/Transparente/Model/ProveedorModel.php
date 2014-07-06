<?php
namespace Transparente\Model;

use Doctrine\ORM\EntityRepository;
use Transparente\Model\Entity\Proveedor;

class ProveedorModel extends EntityRepository
{

    public function findAll()
    {
        return $this->findBy($criteria = [], $orderBy = ['nombre' => 'ASC']);
    }

    public function findByNoDomicilioFiscal()
    {
        $dql = 'SELECT Proveedor
                FROM Transparente\Model\Entity\Proveedor Proveedor
                WHERE Proveedor.domicilio_fiscal IS NULL
                ORDER BY Proveedor.nombre
                ';
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();

    }

    public function save(Proveedor $entity)
    {
        $em = $this->getEntityManager();
        $em->persist($entity);
    }

}