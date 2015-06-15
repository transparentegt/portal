<?php
namespace Transparente\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Transparente\Model\ScraperModel;

/**
 * Proveedor (Vendedor, entidad vendedora)
 *
 * Datos parseables que no se guardan en la base de datos
 * - adjudicado:
 *      bool si tiene o no aprobado un proyecto, valor calculado por JOIN a otra tabla
 * - participa en contrato abierto:
 *      bool si tiene o no registros en la tabla (no existente) de productos, valor calculado por JOIN a otra tabla
 *
 * @link http://guatecompras.gt/info/preguntasFrecProv.aspx#_lbl10
 *
 * @todo Leer inconformidads presentadas
 * @todo Leer inhabilitaciones recibidas
 * @todo Leer publicaciones sin concurso
 *
 * @ORM\Entity(repositoryClass="Transparente\Model\ProveedorModel")
 * @ORM\Table(name="proveedor")
 */
class Proveedor extends AbstractDoctrineEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="Domicilio", cascade="persist")
     * @ORM\JoinColumn(name="id_domicilio_comercial", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $domicilio_comercial;

    /**
     * @ORM\ManyToOne(targetEntity="Domicilio", cascade="persist")
     * @ORM\JoinColumn(name="id_domicilio_fiscal", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $domicilio_fiscal;

    /**
     * Está en domicilio comercial, pero no queremos meter eso en la tabla domicilios.
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $email;

    /**
     * No es autoincrement para usaar el mismo ID que en GTC
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * Fecha de constitución:
     * @ORM\Column(type="date", nullable=true)
     */
    protected $inscripción_fecha_constitución;

    /**
     * Inscripción DEFINITIVA en el Registro Mercantil
     * @ORM\Column(type="date", nullable=true)
     */
    protected $inscripción_fecha_definitiva;

    /**
     * Inscripción PROVISIONAL en el Registro Mercantil
     * @ORM\Column(type="date", nullable=true)
     */
    protected $inscripción_fecha_provisional;

    /**
     * Inscripción en la SAT
     *
     * @ORM\Column(type="date", nullable=true)
     */
    protected $inscripción_fecha_sat;

    /**
     * Número de escritura de constitución
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $inscripción_número_escritura;

    /**
     * @ORM\Column(type="string")
     */
    protected $nit;

    /**
     * Nombre o razón social
     * @ORM\Column(type="string")
     */
    protected $nombre;

    /**
     * @ORM\OneToMany(targetEntity="ProveedorNombreComercial", mappedBy="proveedor", cascade="all")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $nombres_comerciales;

    /**
     * Pagos al proveedor
     *
     * @ORM\OneToMany(targetEntity="Pago", mappedBy="proveedor", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $pagos;

    /**
     * Algunos proveedores no tienen este campo
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $principal_actividad;

    /**
     * Algunos proveedores no tienen este campo
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $principal_trabajo;

    /**
     * Última fecha que se actualizaron los representantes legales
     *
     * @ORM\Column(type="date", nullable=true)
     */
    protected $rep_legales_updated;

    /**
     * Un representante legal puede representar muchas empresas. Una empresa puede estar representada por muchos representantes.
     *
     * @ORM\ManyToMany ( targetEntity = "RepresentanteLegal", inversedBy = "proveedores", cascade = "all" )
     * @ORM\JoinTable (
     *      name               = "proveedor_representado_por",
     *      joinColumns        = { @ORM\JoinColumn (name = "id_proveedor",           referencedColumnName = "id") },
     *      inverseJoinColumns = { @ORM\JoinColumn (name = "id_representante_legal", referencedColumnName = "id") }
     * )
     *
     * @var ArrayCollection
     */
    protected $representantes_legales;

    /**
     * HABILITADO / INHABILITADO
     *
     * @ORM\Column(type="boolean")
     */
    protected $status;

    /**
     * En GTC se muestra como CON/SIN CONTRASEÑA
     *
     * @ORM\Column(type="boolean")
     */
    protected $tiene_acceso_sistema;

    /**
     * Los valores encontrados han sido: SOCIEDAD ANÓNIMA, INDIVIDUAL
     *
     * @todo tiene que ser un listado limitado
     *
     * @ORM\Column(type="string")
     */
    protected $tipo_organización;

    /**
     * Última fecha de actualización por SAT
     * (Datos recibidos de la SAT el: 01.jul.2014 16:45:44)
     *
     * @ORM\Column(type="date")
     */
    protected $updated_sat;

    /**
     * Está en domicilio comercial, pero no queremos meter eso en la tabla domicilios.
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $url;

    public function __construct()
    {
        $this->nombres_comerciales    = new ArrayCollection();
        $this->pagos                  = new ArrayCollection();
        $this->representantes_legales = new ArrayCollection();
    }


    public function addPago(Pago $pago)
    {
        $this->pagos[] = $pago;
    }

    public function appendNombreComercial(ProveedorNombreComercial $nombreComercial)
    {
        $nombreComercial->setProveedor($this);
        $this->nombres_comerciales[] = $nombreComercial;
        return $this;
    }

    public function appendRepresentanteLegal(RepresentanteLegal $repLegal)
    {
        if (isset($this->representantes_legales[$repLegal->getId()])) return $this;
        $repLegal->representa($this);
        if (!isset($this->representantes_legales[$repLegal->getId()])) {
            $this->representantes_legales[$repLegal->getId()] = $repLegal;
        }
        return $this;
    }

    public function getConstFecha ()
    {
        return $this->const_fecha;
    }

    public function getConstNumEscritura ()
    {
        return $this->const_num_escritura;
    }

    public function getDomicilioComercial ()
    {
        return $this->domicilio_comercial;
    }

    public function getDomicilioFiscal ()
    {
        return $this->domicilio_fiscal;
    }

    public function getEmail ()
    {
        return $this->email;
    }

    public function getId ()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getInscripciónFechaConstitución()
    {
        return $this->inscripción_fecha_constitución;
    }

    /**
     * @return \DateTime
     */
    public function getInscripciónFechaDefinitiva()
    {
        return $this->inscripción_fecha_definitiva;
    }

    /**
     * @return \DateTime
     */
    public function getInscripciónFechaProvisional()
    {
        return $this->inscripción_fecha_provisional;
    }

    /**
     * @return \DateTime
     */
    public function getInscripciónFechaSat()
    {
        return $this->inscripción_fecha_sat;
    }

    /**
     * @return mixed
     */
    public function getInscripciónNúmeroEscritura()
    {
        return $this->inscripción_número_escritura;
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

    public function getNombre ()
    {
        return $this->nombre;
    }

    public function getNombresComerciales()
    {
        return $this->nombres_comerciales;
    }

    /**
     * @return mixed
     */
    public function getPrincipalActividad()
    {
        return $this->principal_actividad;
    }

    /**
     * @return mixed
     */
    public function getPrincipalTrabajo()
    {
        return $this->principal_trabajo;
    }

    /**
     * @return Pago[]
     */
    public function getPagos()
    {
        return $this->pagos;
    }


    public function getRepLegalesUpdated ()
    {
        return $this->rep_legales_updated;
    }

    /**
     * Retorna todos los representantes legales del proveedor
     *
     * @return RepresentanteLegal[]
     */
    public function getRepresentantesLegales()
    {
        $desordenados = $this->representantes_legales;
        $ordenados    = [];
        foreach ($desordenados as $entidad) {
            if (in_array($entidad->getNombre(), $ordenados)) throw new \Exception('Nombre duplicado');
            $ordenados[$entidad->getNombre()] = $entidad;
        }
        ksort($ordenados);
        return $ordenados;
    }

    public function getStatus ($human = false)
    {
        $flag = $this->status;
        if ($human) {
            $flag = ($flag) ? 'activo' : 'inactivo';
        }
        return $flag;
    }

    public function getTieneAccesoSistema ($human = false)
    {
        $flag = $this->tiene_acceso_sistema;
        if ($human) {
            $flag = ($flag) ? 'si' : 'no';
        }
        return $flag;
    }

    public function getTipoOrganización ()
    {
        return $this->tipo_organización;
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
     * Retorna la URL para ver el detalle del proveedor en Guatecompras
     *
     * @return string
     */
    public function getUrlGuatecompras()
    {
        $url = 'http://guatecompras.gt/proveedores/consultaDetProvee.aspx?rqp=8&lprv=' . $this->getId();
        return $url;
    }

    public function setConstFecha ($const_fecha)
    {
        $this->const_fecha = $const_fecha;
        return $this;
    }

    public function setDomicilioComercial (\Transparente\Model\Entity\Domicilio $domicilio)
    {
        $this->domicilio_comercial = $domicilio;
        return $this;
    }

    public function setDomicilioFiscal (\Transparente\Model\Entity\Domicilio $domicilio)
    {
        $this->domicilio_fiscal = $domicilio;
        return $this;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = ($email != '[--No Especificado--]') ? $email : null;
    }

    /**
     * @param string $inscripción_fecha_constitución
     */
    public function setInscripciónFechaConstitución($inscripción_fecha_constitución)
    {
        $inscripción_fecha_constitución       = ScraperModel::fecha($inscripción_fecha_constitución);
        $this->inscripción_fecha_constitución = $inscripción_fecha_constitución;
        return $this;
    }

    /**
     * @param string $inscripción_fecha_definitiva
     */
    public function setInscripciónFechaDefinitiva($inscripción_fecha_definitiva)
    {
        $inscripción_fecha_definitiva       = ScraperModel::fecha($inscripción_fecha_definitiva);
        $this->inscripción_fecha_definitiva = $inscripción_fecha_definitiva;
        return $this;
    }

    /**
     * @param string $inscripción_fecha_provisional
     */
    public function setInscripciónFechaProvisional($inscripción_fecha_provisional)
    {
        $inscripción_fecha_provisional       = ScraperModel::fecha($inscripción_fecha_provisional);
        $this->inscripción_fecha_provisional = $inscripción_fecha_provisional;
        return $this;
    }

    /**
     * @param string $inscripción_fecha_sat
     */
    public function setInscripciónFechaSat($inscripción_fecha_sat)
    {
        $inscripción_fecha_sat       = ScraperModel::fecha($inscripción_fecha_sat);
        $this->inscripción_fecha_sat = $inscripción_fecha_sat;
        return $this;
    }

    /**
     * @param mixed $inscripción_número_escritura
     */
    public function setInscripciónNúmeroEscritura($inscripción_número_escritura)
    {
        $this->inscripción_número_escritura = (int) $inscripción_número_escritura;
    }

    public function setNombre ($nombre)
    {
        $nombre = ScraperModel::nombresPropios($nombre);
        $nombre = preg_replace('/\s?sociedad anonima/i', ', S.A.', $nombre);
        $nombre = trim($nombre);
        $this->nombre = $nombre;
        return $this;
    }

    public function setNit ($nit)
    {
        $this->nit = $nit;
        return $this;
    }

    /**
     * @param mixed $principal_actividad
     */
    public function setPrincipalActividad($principal_actividad)
    {
        $this->principal_actividad = ucfirst(strtolower($principal_actividad));
    }

    /**
     * @param mixed $principal_trabajo
     */
    public function setPrincipalTrabajo($principal_trabajo)
    {
        $this->principal_trabajo = ucfirst(strtolower($principal_trabajo));
    }

    /**
     * @param mixed $rep_legales_updated
     */
    public function setRepLegalesUpdated($rep_legales_updated)
    {
        $this->rep_legales_updated = ScraperModel::fecha($rep_legales_updated);
    }

    public function setStatus ($status)
    {
        $this->status = ($status == 'HABILITADO');;
        return $this;
    }

    public function setTieneAccesoSistema ($tiene_acceso_sistema)
    {
        $this->tiene_acceso_sistema = ($tiene_acceso_sistema == 'CON CONTRASEÑA');
        return $this;
    }

    /**
     * @param string $tipo_organización
     */
    public function setTipoOrganización($tipo_organización)
    {
        $this->tipo_organización = mb_strtolower($tipo_organización, 'UTF-8');
    }

    /**
     * @param string $updated_sat
     */
    public function setUpdatedSat($updated_sat)
    {
        $this->updated_sat = ScraperModel::fecha($updated_sat);
        return $this;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = ($url  != '[--No Especificado--]') ? $url : null;
    }
}