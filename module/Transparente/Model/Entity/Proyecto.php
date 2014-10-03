<?php
namespace Transparente\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Proyectos adjudicados
 *
 * @todo Estoy 99% seguro que los no adjudicados son iguales
 *
 * @ORM\Entity(repositoryClass="Transparente\Model\ProyectoModel")
 * @ORM\Table(name="proyecto")
 */
class Proyecto extends AbstractDoctrineEntity
{
    /**
     * NOG - no es autoincrementativo pues usa el mismo ID que GTC
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * Proveedor adjudicado al proyecto
     *
     * @ORM\ManyToMany(targetEntity="Proveedor", inversedBy="proyectos")
     * @ORM\JoinColumn(name="id_proveedor", referencedColumnName="id")
     */
    protected $proveedor;

    /**
     * @ORM\Column(type="string")
     */
    protected $categoría;

    /**
     * @ORM\Column(type="string")
     */
    protected $descripción;

    /**
     * @ORM\Column(type="string")
     */
    protected $modalidad;

    /**
     * @ORM\Column(type="string")
     */
    protected $tipo;

    /**
     * @ORM\Column(type="string")
     */
    protected $comprador;

    /**
     * @ORM\Column(type="string")
     */
    protected $tipoComprador;

    /**
     * @ORM\Column(type="string")
     */
    //protected $unidadCompradora;

    /**
     * @ORM\Column(type="string")
     */
    protected $fechaPublicación;

    /**
     * @ORM\Column(type="string")
     */
    //protected $fechaPresentaciónOfertas;

    /**
     * @ORM\Column(type="string")
     */
    protected $fechaCierreRecepciónOfertas;

    /**
     * @ORM\Column(type="string")
     */
    protected $tipoRecepciónOfertas;

    // protected $finalización;

    /**
     * @ORM\Column(type="string")
     */
    protected $status;


    public function setProveedor(\Transparente\Model\Entity\Proveedor $proveedor)
    {
        $this->proveedor = $proveedor;
    }

}