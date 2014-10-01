<?php
namespace Transparente\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use \Transparente\Model\Entity\AbstractDoctrineEntity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="geo_departamentos")
 */
class GeoDepartamento extends AbstractDoctrineEntity
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
    protected $nombre;

    /**
     * @ORM\OneToMany(targetEntity="GeoMunicipio", mappedBy="departamento")
     */
    private $municipios;

    public function __construct()
    {
        $this->municipios = new ArrayCollection();
    }

    public function getNombre ()
    {
        return $this->nombre;
    }

    public function getMunicipios ()
    {
        return $this->municipios;
    }


}