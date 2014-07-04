<?php
namespace Transparente\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Transparente\Model\Entity\AbstractDoctrineEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="rep_legal_nombre_comercial")
 */
class RepresentanteLegalNombreComercial extends AbstractDoctrineEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="RepresentanteLegal", inversedBy="nombres_comerciales")
     * @ORM\JoinColumn(name="id_rep_legal", referencedColumnName="id")
     */
    protected $representante_legal;

    /**
     * @ORM\Column(type="string")
     */
    protected $nombre;

    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setRepresentanteLegal(\Transparente\Model\Entity\Proveedor $representante_legal)
    {
        $this->representante_legal = $representante_legal;
    }

}