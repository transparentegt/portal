<?php
namespace Transparente\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Transparente\Model\Entity\AbstractDoctrineEntity;
use Transparente\Model\ScraperModel;

/**
 * Vimos que en la vista de proveedor se listan hasta 5 nombres comerciales pero que al meter el ID del proveedor en la
 * vista para ver todos los nombres comerciales listan más, esa misma URL se le puede pasar el parámetro de un
 * proveedor con menos de 5 nomobres comerciales y también los lista
 *
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
     * @ORM\JoinColumn(name="id_proveedor", referencedColumnName="id", onDelete="CASCADE")
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