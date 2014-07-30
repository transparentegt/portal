<?php
namespace Transparente\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Transparente\Model\Entity\AbstractDoctrineEntity;

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
     * @ORM\JoinColumn(name="id_partido_político", referencedColumnName="id")
     */
    protected $partido_político;


    public function getId()
    {
        return $this->id;
    }


    public function setMunicipio(GeoMunicipio $municipio)
    {
        $this->municipio = $municipio;
        return $this;
    }

    public function setPartidoPolitico(PartidoPolitico $partido_político)
    {
        $this->partido_político = $partido_político;
        return $this;
    }
}