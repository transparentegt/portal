<?php
namespace Transparente\Model;

class DomicilioModel extends AbstractModel
{
    public  function detectarMunicipio($departamento, $municipio, $municipioField = 'nombre_guatecompras')
    {
        $original = [
            'departamento' => $departamento,
            'municipio'    => $municipio,
        ];

        $departamento = ScraperModel::nombresPropios($departamento);
        $municipio    = ScraperModel::nombresPropios($municipio);

        $dql = "SELECT municipio
                FROM Transparente\Model\Entity\GeoMunicipio municipio
                JOIN municipio.departamento departamento
                WHERE departamento.nombre = ?1 AND municipio.$municipioField = ?2 ";
        $rs = $this->getEntityManager()->createQuery($dql)
            ->setParameter(1, $departamento)
            ->setParameter(2, $municipio)
            ->getResult();
        switch (count($rs)) {
            case 0:
                throw new \Exception("No se encontró '$municipio', '$departamento' (datos originales: {$original['municipio']}, {$original['departamento']})\nDQL: $dql");
                break;
            case 1;
            $municipio = $rs[0];
            break;
            default:
                echo '<pre><strong>DEBUG::</strong> '.__FILE__.' +'.__LINE__."\n"; var_dump(\Doctrine\Common\Util\Debug::dump($rs)); die();
                throw new \Exception("Se se encontró más de un municipio con los datos: departamento = '$departamento', municipio = '$municipio'\n");
        }
        return $municipio;
    }

    public function createFromScrappedData($data)
    {
        if (empty($data['departamento']) || empty($data['municipio'])) {
            return null;
        }
        $municipio = $this->detectarMunicipio($data['departamento'], $data['municipio']);

        $domicilio = new \Transparente\Model\Entity\Domicilio();
        $domicilio->exchangeArray($data);
        $domicilio->setMunicipio($municipio);
        return $domicilio;
    }

}