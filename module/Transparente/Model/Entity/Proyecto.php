<?php
namespace Transparente\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Transparente\Model\Entity\AbstractDoctrineEntity;
use Transparente\Model\ScraperModel;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Transparente\Model\ProyectoModel")
 * @ORM\Table(name="proyecto")
 */
class Proyecto extends AbstractDoctrineEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Proveedor", inversedBy="proyectos")
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

    public function setNombre ($nombre)
    {
        $nombre = ScraperModel::nombresPropios($nombre);
        $nombre = str_replace('Sociedad Anonima', 'S.A.', $nombre);
        $this->nombre = $nombre;
        return $this;
    }

    public function setProveedor(\Transparente\Model\Entity\Proveedor $proveedor)
    {
        $this->proveedor = $proveedor;
    }

}