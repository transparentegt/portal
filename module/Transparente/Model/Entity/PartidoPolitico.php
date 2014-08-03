<?php
namespace Transparente\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Transparente\Model\Entity\AbstractDoctrineEntity;

/**
 * Partido Político
 *
 * @ORM\Entity(repositoryClass="Transparente\Model\PartidoPoliticoModel")
 * @ORM\Table(name="partido_político")
 *
 * @todo Conseguir los logos de los partidos políticos
 */
class PartidoPolitico extends AbstractDoctrineEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $nombre;

    /**
     * @ORM\Column(type="string")
     */
    protected $iniciales;

    /**
     * @ORM\OneToMany(targetEntity="EmpleadoMunicipal", mappedBy="municipio")
     */
    private $empleados_municipales;


    public function __construct()
    {
        $this->empleados_municipales = new ArrayCollection();
    }

    public function appendEmpleadoMunicipal(EmpleadoMunicipal $empleadoMunicipal)
    {
        $empleadoMunicipal->setPartidoPolítico($this);
        $this->empleados_municipales[] = $empleadoMunicipal;
        return $this;
    }

    public function getId ()
    {
        return $this->id;
    }

    public function getEmpleadosMunicipales()
    {
        return $this->empleados_municipales;
    }
}