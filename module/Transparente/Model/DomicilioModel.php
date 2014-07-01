<?php
namespace Transparente\Model;

use Doctrine\ORM\EntityRepository;

class DomicilioModel extends EntityRepository
{

    private function detectarMunicipio($data)
    {
    }

    public function createFromScrappedData($data)
    {
        if (empty($data['departamento']) || empty($data['municipio'])) {
            return null;
        }
        $departamento = $data['departamento'];
        $departamento = strtolower($departamento);
        $departamento = ucfirst($departamento);
        $municipio    = $data['municipio'];
        $municipio    = strtolower($municipio);
        $municipio    = ucfirst($municipio);

        $dql = 'SELECT municipio
                FROM Transparente\Model\Entity\GeoMunicipio municipio
                JOIN Transparente\Model\Entity\GeoDepartamento departamento
                WHERE departamento.nombre = ?1 AND municipio.nombre = ?2 ';
        $rs = $this->getEntityManager()->createQuery($dql)
            ->setParameter(1, $departamento)
            ->setParameter(2, $municipio)
            ->getResult();
        $municipio = NULL;
        switch (count($rs)) {
            case 0:
                throw new \Exception("No se encontró un municipio con los datos: departamento=$departamento, municipio=$municipio");
                break;
            case 1;
                $municipio = $rs[0];
                break;
            default:
                throw new \Exception("Se se encontró más de un municipio con los datos: departamento=$departamento, municipio=$municipio");
        }
        $domicilio = new \Transparente\Model\Entity\Domicilio();
        $domicilio->exchangeArray($data);
        $domicilio->setMunicipio($municipio);
        return $domicilio;
    }

}