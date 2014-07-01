<?php
namespace Transparente\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use \Transparente\Model\Entity\AbstractDoctrineEntity;

/**
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
     * @ORM\Column(type="string")
     */
    protected $telefonos;

    /**
     * @ORM\Column(type="string")
     */
    protected $fax;

    /**
     * @ORM\Column(type="datetime")
     */
    // protected $updated;

    /**
     * @ORM\ManyToOne(targetEntity="GeoMunicipio")
     * @ORM\JoinColumn(name="id_municipio", referencedColumnName="id")
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
        $this->direccion = $direccion;
        return $this;
    }

    public function getTelefonos ()
    {
        return $this->telefonos;
    }

    public function setTelefonos ($telefonos)
    {
        $this->telefonos = $telefonos;
        return $this;
    }

    public function getFax ()
    {
        return $this->fax;
    }

    public function setFax ($fax)
    {
        $this->fax = $fax;
        return $this;
    }

    public function getUpdated ()
    {
        return $this->updated;
    }

    public function setUpdated ($updated)
    {
        $this->updated = $updated;
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