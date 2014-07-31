<?php
namespace Transparente\Model;

use Doctrine\ORM\EntityRepository;
use Transparente\Model\Entity\Proveedor;

class EmpleadoMunicipalModel extends EntityRepository
{

    /**
     * Buscar todos los empleados municipales que tengan apellidos de los representantes legales del proveedor
     *
     * @param Proveedor $proveedor
     * @return Transparente\Model\Entity\EmpleadoMunicipal[]
     */
    public function findByRepresentantesLegalesDelProveedor(Proveedor $proveedor)
    {
        $dql = 'SELECT EmpleadoMunicipal
                FROM \Transparente\Model\Entity\RepresentanteLegal RepresentanteLegal
                JOIN RepresentanteLegal.proveedores                Proveedor
                JOIN \Transparente\Model\Entity\EmpleadoMunicipal  EmpleadoMunicipal
                WHERE Proveedor.id = ?1 AND (
                       (RepresentanteLegal.apellido1 = EmpleadoMunicipal.apellido1)
                    OR (RepresentanteLegal.apellido1 = EmpleadoMunicipal.apellido2)
                    OR (RepresentanteLegal.apellido1 = EmpleadoMunicipal.apellido3)

                    OR (RepresentanteLegal.apellido2 = EmpleadoMunicipal.apellido1)
                    OR (RepresentanteLegal.apellido2 = EmpleadoMunicipal.apellido2)
                    OR (RepresentanteLegal.apellido2 = EmpleadoMunicipal.apellido3)
                )
                ';
        $rs = $this->getEntityManager()->createQuery($dql)
            ->setParameter(1, $proveedor->getId())
            ->getResult();
        return $rs;

    }
}