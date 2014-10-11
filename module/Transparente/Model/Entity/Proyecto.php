<?php
namespace Transparente\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Transparente\Model\ScraperModel;

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
     * Proveedores adjudicado al proyecto
     *
     * @ORM\ManyToMany(targetEntity="Proveedor", mappedBy="proyectos")
     */
    protected $proveedores;

    /**
     * @ORM\Column(type="string")
     */
    protected $categoría;

    /**
     * @ORM\Column(type="string")
     */
    protected $modalidad;

    /**
     * Nombre del proyecto
     *
     * Le pusimos "nombre" para estandarizar que toda entidad tenga un nombre, pero en GTC dice "descripción"
     * Tiene de constrain en GTC que es un campo de varchar(200) detectado por los nombres más largos
     *
     * @ORM\Column(type="string")
     */
    protected $nombre;

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

    public function __construct() {
        $this->proveedores = new ArrayCollection();
    }

    public function addProveedor(Proveedor $proveedor)
    {
        $this->proveedores[] = $proveedor;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @param mixed $nombre
     */
    public function setNombre($nombre)
    {
        $nombre       = ScraperModel::nombresPropios($nombre);
        $this->nombre = $nombre;
    }


}