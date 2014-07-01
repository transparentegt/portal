<?php
namespace Transparente\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Transparente\Model\Entity\AbstractDoctrineEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="proveedor_nombres_comerciales")
 */
class ProveedorNombreComercial extends AbstractDoctrineEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Proveedor", inversedBy="nombres_comerciales")
     * @ORM\JoinColumn(name="id_proveedor", referencedColumnName="id")
     */
    protected $proveedor;

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

    public function setProveedor(\Transparente\Model\Entity\Proveedor $proveedor)
    {
        $this->proveedor = $proveedor;
    }

}