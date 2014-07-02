<?php
namespace Transparente\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Transparente\Model\Entity\AbstractDoctrineEntity;

/**
 * @ORM\Entity(repositoryClass="Transparente\Model\ProveedorModel")
 * @ORM\Table(name="proveedores")
 */
class Proveedor extends AbstractDoctrineEntity
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
    protected $nit;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $status;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $tiene_acceso_sistema;

    /**
     * @ORM\ManyToOne(targetEntity="Domicilio", cascade="persist")
     * @ORM\JoinColumn(name="id_domicilio_fiscal", referencedColumnName="id")
     */
    protected $domicilio_fiscal;

    /**
     * @ORM\ManyToOne(targetEntity="Domicilio", cascade="persist")
     * @ORM\JoinColumn(name="id_domicilio_comercial", referencedColumnName="id")
     */
    protected $domicilio_comercial;

    /**
     * @ORM\Column(type="string")
     */
    protected $url;

    /**
     * @ORM\Column(type="string")
     */
    protected $email;

    /**
     * @ORM\Column(type="string")
     */
    protected $rep_legales_updated;

    /**
     * @ORM\Column(type="string")
     */
    protected $tipo_organizacion;

    /**
     * @ORM\Column(type="string")
     */
    protected $const_num_escritura;

    /**
     * @ORM\Column(type="string")
     */
    protected $const_fecha;

    /**
     * @ORM\Column(type="string")
     */
    protected $inscripcion_provisional;

    /**
     * @ORM\Column(type="string")
     */
    protected $inscripcion_definitiva;

    /**
     * @ORM\Column(type="string")
     */
    protected $inscripcion_sat;

    /**
     * @ORM\OneToMany(targetEntity="ProveedorNombreComercial", mappedBy="proveedor", cascade="persist")
     */
    protected $nombres_comerciales;

    public function getId ()
    {
        return $this->id;
    }

    public function getNombre ()
    {
        return $this->nombre;
    }

    public function setNombre ($nombre)
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function __construct()
    {
        $this->nombres_comerciales = new ArrayCollection();
    }

    public function appendNombreComercial(\Transparente\Model\Entity\ProveedorNombreComercial $nombreComercial)
    {
        $nombreComercial->setProveedor($this);
        $this->nombres_comerciales[] = $nombreComercial;
        return $this;
    }

    public function getNombresComerciales()
    {
        return $this->nombres_comerciales;
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

    public function getUrl ()
    {
        return $this->url;
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

    public function getRepLegalesUpdated ()
    {
        return $this->rep_legales_updated;
    }

    public function setRepLegalesUpdated ($rep_legales_updated)
    {
        $this->rep_legales_updated = $rep_legales_updated;
        return $this;
    }

    public function getTipoOrganizacion ()
    {
        return $this->tipo_organizacion;
    }

    public function setTipoOrganizacion ($tipo_organizacion)
    {
        $this->tipo_organizacion = $tipo_organizacion;
        return $this;
    }

    public function getConstNumEscritura ()
    {
        return $this->const_num_escritura;
    }

    public function setConstNumEscritura ($const_num_escritura)
    {
        $this->const_num_escritura = $const_num_escritura;
        return $this;
    }

    public function getConstFecha ()
    {
        return $this->const_fecha;
    }

    public function setConstFecha ($const_fecha)
    {
        $this->const_fecha = $const_fecha;
        return $this;
    }

    public function getInscripcionProvisional ()
    {
        return $this->inscripcion_provisional;
    }

    public function setInscripcionProvisional ($inscripcion_provisional)
    {
        $this->inscripcion_provisional = $inscripcion_provisional;
        return $this;
    }

    public function getInscripcionDefinitiva ()
    {
        return $this->inscripcion_definitiva;
    }

    public function setInscripcionDefinitiva ($inscripcion_definitiva)
    {
        $this->inscripcion_definitiva = $inscripcion_definitiva;
        return $this;
    }

    public function getInscripcionSat ()
    {
        return $this->inscripcion_sat;
    }

    public function setInscripcionSat ($inscripcion_sat)
    {
        $this->inscripcion_sat = $inscripcion_sat;
        return $this;
    }
}