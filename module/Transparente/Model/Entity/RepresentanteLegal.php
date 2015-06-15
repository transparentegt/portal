<?php
namespace Transparente\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Transparente\Model\Entity\AbstractDoctrineEntity;
use Transparente\Model\Entity\RepresentanteLegalNombreComercial;
use Transparente\Model\ScraperModel;

/**
 * El representante legal usa la misma estructura que el proveedor, incluso para scrapearlo usa la misma URL
 *
 * @ORM\Entity(repositoryClass="Transparente\Model\RepresentanteLegalModel")
 * @ORM\Table(name="rep_legal")
 *
 * @link http://guatecompras.gt/proveedores/consultaDetProvee.aspx?rqp=10&lprv=421
 *
 * @todo Un representante legal puede tener nombres comerciales?
 * @todo Un representante legal puede tener representantes legales?
 */
class RepresentanteLegal extends AbstractDoctrineEntity
{
    /**
     * no es autoincrement para usaar el mismo ID que en GTC
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * Datos recibidos de la SAT
     * @ORM\Column(type="datetime")
     */
    // protected $actualizado_sat;

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
     * Inscripción en la SAT
     *
     * @ORM\Column(type="date", nullable=true)
     */
    protected $inscripción_fecha_sat;

    /**
     * @ORM\Column(type="string")
     */
    protected $nit;

    /**
     * @ORM\OneToMany(targetEntity="RepresentanteLegalNombreComercial", mappedBy="representante_legal", cascade="persist")
     */
    protected $nombres_comerciales;

    /**
     * GTC: HABILITADO / INHABILITADO
     *
     * @ORM\Column(type="boolean")
     */
    protected $status;

    /**
     * @ORM\Column(type="string")
     */
    protected $tipo;

    /**
     * En GTC se muestra como CON/SIN CONTRASEÑA
     *
     * @ORM\Column(type="boolean")
     */
    protected $tiene_acceso_sistema = false;

    /**
     * @ORM\ManyToOne(targetEntity="Domicilio", cascade="persist")
     * @ORM\JoinColumn(name="id_domicilio_fiscal", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $domicilio_fiscal;

    /**
     * @ORM\ManyToOne(targetEntity="Domicilio", cascade="persist")
     * @ORM\JoinColumn(name="id_domicilio_comercial", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $domicilio_comercial;

    /**
     * Está en domicilio comercial, pero no queremos meter eso en la tabla domicilios
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $url;

    /**
     * está en domicilio comercial, pero no queremos meter eso en la tabla domicilios
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $email;

    /**
     * Un representante legal puede representar muchas empresas. Una empresa puede estar representada por muchos representantes.
     *
     * @ORM\ManyToMany(targetEntity="Proveedor", mappedBy="representantes_legales", cascade="all")
     * @ORM\JoinTable(name="proveedor_representado_por")
     * @ORM\JoinColumn(onDelete="CASCADE")
     *
     * @var ArrayCollection
     */
    protected $proveedores;

    /**
     * Un representante legal puede tener muchos representantes legales.
     *
     * @ORM\ManyToMany ( targetEntity = "RepresentanteLegal", cascade = "all" )
     * @ORM\JoinTable (
     *      name               = "representante_representado_por",
     *      joinColumns        = { @ORM\JoinColumn (name = "id_representante_legal", referencedColumnName = "id") },
     *      inverseJoinColumns = { @ORM\JoinColumn (name = "id_representado_por",    referencedColumnName = "id") }
     * )
     * @ORM\JoinColumn(onDelete="CASCADE")
     *
     * @var ArrayCollection
     *
     * @todo Esta relación no debería de existir y debería de unificarse como proveedor
     */
    protected $representantes_legales;

    /**
     * Última fecha de actualización por SAT
     * (Datos recibidos de la SAT el: 01.jul.2014 16:45:44)
     *
     * @ORM\Column(type="date")
     */
    protected $updated_sat;

    /**
     * Tipo de representante legal, tiene que ser un listado limitado.
     * Los valores encontrados han sido: SOCIEDAD ANÓNIMA, INDIVIDUAL
     *
     * @ORM\Column(type="string")
     */
    // protected $tipo_organizacion;

    /**
     * @ORM\Column(type="string")
     */
    // protected $const_fecha;

    /**
     * Número de escritura de constitucioń (WTF? será número entero)
     *
     * @ORM\Column(type="string")
     */
    // protected $const_num_escritura;

    /**
     * @ORM\Column(type="string")
     */
    // protected $inscripcion_provisional;

    /**
     * @ORM\Column(type="string")
     */
    // protected $inscripcion_definitiva;

    /**
     * @ORM\Column(type="string")
     */
    // protected $inscripcion_sat;

    public function __construct()
    {
        $this->nombres_comerciales    = new ArrayCollection();
        $this->proveedores            = new ArrayCollection();
        $this->representantes_legales = new ArrayCollection();
    }

    public function getId ()
    {
        return $this->id;
    }

    public function appendNombreComercial(RepresentanteLegalNombreComercial $nombreComercial)
    {
        $nombreComercial->setRepresentanteLegal($this);
        $this->nombres_comerciales[] = $nombreComercial;
        return $this;
    }

    public function appendRepresentanteLegal(RepresentanteLegal $repLegal)
    {
        $this->representantes_legales[] = $repLegal;
        return $this;
    }

    /**
     * Retorna el nombre completo del representante legal
     */
    public function getNombre()
    {
        $apellidos = trim("{$this->apellido1} {$this->apellido2} {$this->apellido3}");
        $nombres   = trim("{$this->nombre1} {$this->nombre2}");
        return "$apellidos, $nombres";
    }

    public function getNombresComerciales()
    {
        return $this->nombres_comerciales;
    }

    /**
     * @return \DateTime
     */
    public function getInscripciónFechaSat()
    {
        return $this->inscripción_fecha_sat;
    }

    /**
     * Se retorna el NIT con el guión del dígito verificador final
     *
     * @return string
     *
     * @todo validar cuando el nit es inválido
     */
    public function getNit()
    {
        $nit = $this->nit;
        $nit = substr($nit, 0, strlen($nit) -1) . '-' . substr($nit, -1, 1);
        return $nit;
    }

    public function getRepresentantesLegales()
    {
        return $this->representantes_legales;
    }

    public function setApellido1($apellido1)
    {
        $this->apellido1 = ScraperModel::nombresPropios($apellido1);
        return $this;
    }

    public function setApellido2($apellido2)
    {
        $this->apellido2 = ScraperModel::nombresPropios($apellido2);
        return $this;
    }

    public function setApellido3($apellido3)
    {
        $this->apellido3 = ScraperModel::nombresPropios($apellido3);
        return $this;
    }

    public function setNombre1($nombre1)
    {
        $this->nombre1 = ScraperModel::nombresPropios($nombre1);
        return $this;
    }

    public function setNombre2($nombre2)
    {
        $this->nombre2 = ScraperModel::nombresPropios($nombre2);
        return $this;
    }

    /**
     * @param string $inscripción_fecha_sat
     */
    public function setInscripciónFechaSat($inscripción_fecha_sat)
    {
        if ($inscripción_fecha_sat) {
            $inscripción_fecha_sat       = ScraperModel::fecha($inscripción_fecha_sat);
            $this->inscripción_fecha_sat = $inscripción_fecha_sat;
        }
        return $this;
    }

    public function setNit ($nit)
    {
        $this->nit = $nit;
        return $this;
    }

    public function getStatus ($human = false)
    {
        $flag = $this->status;
        if ($human) {
            $flag = ($flag) ? 'activo' : 'inactivo';
        }
        return $flag;
    }

    public function setStatus ($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getTieneAccesoSistema ($human = false)
    {
        $flag = $this->tiene_acceso_sistema;
        if ($human) {
            $flag = ($flag) ? 'si' : 'no';
        }
        return $flag;
    }

    public function setTieneAccesoSistema ($tiene_acceso_sistema)
    {
        $this->tiene_acceso_sistema = $tiene_acceso_sistema;
        return $this;
    }

    public function getDomicilioFiscal ()
    {
        return $this->domicilio_fiscal;
    }

    public function setDomicilioFiscal (\Transparente\Model\Entity\Domicilio $domicilio)
    {
        $this->domicilio_fiscal = $domicilio;
        return $this;
    }

    public function getDomicilioComercial ()
    {
        return $this->domicilio_comercial;
    }

    public function setDomicilioComercial (\Transparente\Model\Entity\Domicilio $domicilio)
    {
        $this->domicilio_comercial = $domicilio;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdatedSat()
    {
        return $this->updated_sat;
    }

    public function getUrl ()
    {
        return $this->url;
    }

    /**
     * @param string $updated_sat
     */
    public function setUpdatedSat($updated_sat)
    {
        if ($updated_sat) {
            $this->updated_sat = ScraperModel::fecha($updated_sat);
        }
        return $this;
    }

    public function setUrl ($url)
    {
        $this->url = $url;
        return $this;
    }

    public function getEmail ()
    {
        return $this->email;
    }

    public function setEmail ($email)
    {
        $this->email = $email;
        return $this;
    }

    public function getProveedores()
    {
        return $this->proveedores;
    }

    public function getTipo()
    {
        return $this->tipo;
    }

    public function representa(Proveedor $proveedor)
    {
        if (!isset($this->proveedores[$proveedor->getId()])) {
            $this->proveedores[] = $proveedor;
        }
        return $this;
    }
}