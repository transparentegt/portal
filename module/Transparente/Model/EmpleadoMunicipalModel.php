<?php
namespace Transparente\Model;

use Transparente\Model\Entity\Proveedor;

class EmpleadoMunicipalModel extends AbstractModel
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
                WHERE Proveedor.id = ?1
                    AND (
                            (
                                    RepresentanteLegal.apellido1 IS NOT NULL
                                AND RepresentanteLegal.apellido1 <> \'\'
                                AND (
                                       (RepresentanteLegal.apellido1 = EmpleadoMunicipal.apellido1)
                                    OR (RepresentanteLegal.apellido1 = EmpleadoMunicipal.apellido2)
                                    OR (RepresentanteLegal.apellido1 = EmpleadoMunicipal.apellido3)
                                )
                            ) OR (
                                    RepresentanteLegal.apellido2 IS NOT NULL
                                AND RepresentanteLegal.apellido2 <> \'\'
                                AND (
                                       (RepresentanteLegal.apellido2 = EmpleadoMunicipal.apellido1)
                                    OR (RepresentanteLegal.apellido2 = EmpleadoMunicipal.apellido2)
                                    OR (RepresentanteLegal.apellido2 = EmpleadoMunicipal.apellido3)
                                )
                            ) OR (
                                    RepresentanteLegal.apellido3 IS NOT NULL
                                AND RepresentanteLegal.apellido3 <> \'\'
                                AND (
                                       (RepresentanteLegal.apellido3 = EmpleadoMunicipal.apellido1)
                                    OR (RepresentanteLegal.apellido3 = EmpleadoMunicipal.apellido2)
                                    OR (RepresentanteLegal.apellido3 = EmpleadoMunicipal.apellido3)
                                )
                            )
                    )
                ORDER BY EmpleadoMunicipal.apellido1, EmpleadoMunicipal.apellido2,
                         EmpleadoMunicipal.nombre1,   EmpleadoMunicipal.nombre2
                ';
        $rs = $this->getEntityManager()->createQuery($dql)
            ->setParameter(1, $proveedor->getId())
            ->getResult();
        return $rs;

    }

    /**
     * Paginado
     *
     * @return Paginator
     */
    public function getPaginator(\Zend\Stdlib\Parameters $params = null)
    {
        $queryOptions = [
            'order'  => 'EmpleadoMunicipal.apellido1',
            'sort'   => 'ASC',
            'filter' => false,
        ];
        if ($params) {
            $queryOptions = array_merge($queryOptions, $params->toArray());
        }

        $dql = 'SELECT EmpleadoMunicipal
                FROM Transparente\Model\Entity\EmpleadoMunicipal EmpleadoMunicipal
                JOIN EmpleadoMunicipal.municipio                 Municipio
                JOIN Municipio.departamento                      Departamento

                ';
        if ($queryOptions['filter']) {
            $dql .= "
                WHERE EmpleadoMunicipal.nombre1   LIKE '%{$queryOptions['filter']}%'
                   OR EmpleadoMunicipal.nombre2   LIKE '%{$queryOptions['filter']}%'
                   OR EmpleadoMunicipal.apellido1 LIKE '%{$queryOptions['filter']}%'
                   OR EmpleadoMunicipal.apellido2 LIKE '%{$queryOptions['filter']}%'
                   OR EmpleadoMunicipal.apellido3 LIKE '%{$queryOptions['filter']}%'
                   OR EmpleadoMunicipal.cargo     LIKE '%{$queryOptions['filter']}%'
                   OR Municipio.nombre            LIKE '%{$queryOptions['filter']}%'
                   OR Departamento.nombre         LIKE '%{$queryOptions['filter']}%'
            ";
        }
        $dql .= " ORDER BY {$queryOptions['order']} {$queryOptions['sort']}";

        $paginator = $this->getPaginatorFromDql($dql);
        return $paginator;
    }
}