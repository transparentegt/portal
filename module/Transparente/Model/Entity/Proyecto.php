<?php
namespace Transparente\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Transparente\Model\ScraperModel;

/**
 * Proyectos adjudicados
 *
 * @todo Estoy 99% seguro que los no adjudicados son iguales
 * @todo ¿Qué carajo es una constancia de disponibilidad presupuestaria CDP?
 *
 * @ORM\Entity(repositoryClass="Transparente\Model\ProyectoModel")
 * @ORM\Table(name="proyecto")
 */
class Proyecto extends AbstractDoctrineEntity
{
    /**
     * @ORM\Column(type="string")
     */
    protected $categoría;

    /**
     * Entidad compradora: Ministerio de finanzas, etc
     *
     * @todo ¿Deberíamos de crear una entidad para la entidad compradora?
     * @ORM\Column(type="string")
     */
    protected $entidad_compradora;

    /**
     * @ORM\Column(type="string")
     */
    protected $entidad_compradora_tipo;

    /**
     * Unidad compradora
     *
     * @todo ¿Deberíamos de crear una entidad para la unidad de la entidad compradora?
     * @ORM\Column(type="string")
     */
    protected $entidad_compradora_unidad;

    /**
     * @ORM\Column(type="date")
     */
    protected $fecha_cierre_ofertas;

    /**
     * @ORM\Column(type="date")
     */
    protected $fecha_finalización;

    /**
     * @ORM\Column(type="date")
     */
    protected $fecha_presentación_ofertas;

    /**
     * @ORM\Column(type="date")
     */
    protected $fecha_publicación;

    /**
     * NOG - no es autoincrementativo pues usa el mismo ID que GTC
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $id;

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
     * Pagos por el proyecto
     *
     * @ORM\OneToMany(targetEntity="Pago", mappedBy="proyecto")
     */
    protected $pagos;

    /**
     * @ORM\Column(type="string")
     */
    protected $status;

    /**
     * @ORM\Column(type="string")
     */
    protected $tipo;

    /**
     * @ORM\Column(type="string")
     */
    protected $tipo_recepción_oferta;

    public function __construct()
    {
        $this->pagos = new ArrayCollection();
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
     * @param string $nombre
     */
    public function setEntidadCompradora($nombre)
    {
        $nombre                   = ScraperModel::nombresPropios($nombre);
        $this->entidad_compradora = $nombre;
    }

    /**
     * @param string $nombre
     */
    public function setEntidadCompradoraUnidad($nombre)
    {
        $nombre                          = ScraperModel::nombresPropios($nombre);
        $this->entidad_compradora_unidad = $nombre;
    }

    /**
     * @param mixed $fecha_cierre_ofertas
     */
    public function setFechaCierreOfertas($fecha_cierre_ofertas)
    {
        $this->fecha_cierre_ofertas = ScraperModel::fecha($fecha_cierre_ofertas);
    }

    /**
     * @param mixed $fecha_finalización
     */
    public function setFechaFinalización($fecha_finalización)
    {
        $this->fecha_finalización = ScraperModel::fecha($fecha_finalización);
    }

    /**
     * @param mixed $fecha_presentación_ofertas
     */
    public function setFechaPresentaciónOfertas($fecha_presentación_ofertas)
    {
        $this->fecha_presentación_ofertas = ScraperModel::fecha($fecha_presentación_ofertas);
    }

    /**
     * @param string $fecha_publicación
     */
    public function setFechaPublicación($fecha_publicación)
    {
        $this->fecha_publicación = ScraperModel::fecha($fecha_publicación);
    }

    /**
     * @param string $nombre
     */
    public function setNombre($nombre)
    {
        $nombre       = ScraperModel::nombresPropios($nombre);
        $this->nombre = $nombre;
    }
}