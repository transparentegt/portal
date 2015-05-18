<?php
namespace Transparente\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Transparente\Model\Entity\AbstractDoctrineEntity;
use Transparente\Model\ScraperModel;

/**
 * Empleado municipal, miembro del consejo municipal
 *
 * Los empleados municipales son relegidos cada 4 años, no deberían de cambiar
 *
 * @ORM\Entity(repositoryClass="Transparente\Model\EmpleadoMunicipalModel")
 * @ORM\Table(name="empleado_municipal")
 *
 * @todo Conseguir una fuente digital actualizable
 */
class EmpleadoMunicipal extends AbstractDoctrineEntity
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
    protected $nombre1;

    /**
     * @ORM\Column(type="string")
     */
    protected $nombre2;

    /**
     * @ORM\Column(type="string")
     */
    protected $apellido1;

    /**
     * @ORM\Column(type="string")
     */
    protected $apellido2;

    /**
     * @ORM\Column(type="string")
     */
    protected $apellido3;

    /**
     * @ORM\Column(type="string")
     */
    protected $cargo;

    /**
     * @ORM\ManyToOne(targetEntity="GeoMunicipio", inversedBy="empleados_municipales")
     * @ORM\JoinColumn(name="id_municipio", referencedColumnName="id")
     */
    protected $municipio;

    /**
     * @ORM\ManyToOne(targetEntity="PartidoPolitico", inversedBy="empleados_municipales")
     * @ORM\JoinColumn(name="id_partido_político", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $partido_político;

    /**
     * @return PartidoPolitico
     */
    public function getPartidoPolítico()
    {
        return $this->partido_político;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCargo()
    {
        return $this->cargo;
    }

    public function getMunicipio()
    {
        return $this->municipio;
    }

    /**
     * Retorna el nombre completo
     */
    public function getNombre()
    {
        $apellidos = trim("{$this->apellido1} {$this->apellido2} {$this->apellido3}");
        $nombres   = trim("{$this->nombre1} {$this->nombre2}");
        return "$apellidos, $nombres";
    }

    public function setApellido1($apellido1)
    {
        $this->apellido1 = ScraperModel::nombresPropios($apellido1);
        return $this;
    }

    public function setApellido2($apellido2)
    {
        $this->apellido2 = ScraperModel::nombresPropios($apellido2);
        return $this;
    }

    public function setApellido3($apellido3)
    {
        $this->apellido3 = ScraperModel::nombresPropios($apellido3);
        return $this;
    }

    public function setMunicipio(GeoMunicipio $municipio)
    {
        $this->municipio = $municipio;
        return $this;
    }

    public function setNombre1($nombre1)
    {
        $this->nombre1 = ScraperModel::nombresPropios($nombre1);
        return $this;
    }

    public function setNombre2($nombre2)
    {
        $this->nombre2 = ScraperModel::nombresPropios($nombre2);
        return $this;
    }

    public function setPartidoPolitico(PartidoPolitico $partido_político)
    {
        $this->partido_político = $partido_político;
        return $this;
    }
}