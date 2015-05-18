<?php
namespace Transparente\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use \Transparente\Model\Entity\AbstractDoctrineEntity;
use Transparente\Model\ScraperModel;

/**
 * Entidad genérica de domicilios
 *
 * Se usan varios domicilios en el sistema, domicilio fisca, domicilio comercial, etc. Que hueva estar copiando esa
 * estructura en cada tabla y varias veces. Mejor lo metemos todas las direcciones en una tabla con la misma estructura
 * y hacemos llaves foraneas que apunten a las direcciones aquí guardadas
 *
 * @ORM\Entity(repositoryClass="Transparente\Model\DomicilioModel")
 * @ORM\Table(name="domicilios")
 */
class Domicilio extends AbstractDoctrineEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $direccion;

    /**
     *  Meter todos en un solo campo, porque pelan
     * @ORM\Column(type="string", nullable=true)
     */
    protected $telefonos;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $fax;

    /**
     * @ORM\ManyToOne(targetEntity="GeoMunicipio",cascade={"persist"}))
     * @ORM\JoinColumn(name="id_municipio", referencedColumnName="id", onDelete="CASCADE")
     */
    private $municipio;

    public function getId ()
    {
        return $this->id;
    }

    public function getDireccion ()
    {
        return $this->direccion;
    }

    public function setDireccion ($direccion)
    {
        $this->direccion = ScraperModel::nombresPropios($direccion);
        return $this;
    }

    public function getTelefonos ()
    {
        return $this->telefonos;
    }

    public function setTelefonos ($telefonos)
    {
        $this->telefonos = ($telefonos != '[--No Especificado--]') ? $telefonos : null;
        return $this;
    }

    public function getFax ()
    {
        return $this->fax;
    }

    public function setFax ($fax)
    {
        $this->fax = ($fax != '[--No Especificado--]') ? $fax : null;
        return $this;
    }

    public function getMunicipio ()
    {
        return $this->municipio;
    }

    public function setMunicipio ($municipio)
    {
        $this->municipio = $municipio;
        return $this;
    }

}