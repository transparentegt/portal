<?php
namespace Transparente\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Transparente\Model\ScraperModel;

/**
 * Pagos para proyectos por proveedor
 *
 * @ORM\Entity(repositoryClass="Transparente\Model\PagoModel")
 * @ORM\Table(name="pago")
 */
class Pago extends AbstractDoctrineEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="decimal", precision=20, scale=2)
     * @var float
     */
    protected $monto;

    /**
     * Proveedor a quién se le pagó
     *
     * @ORM\ManyToOne(targetEntity="Proveedor", inversedBy="pagos", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $proveedor;

    /**
     * Proyecto que generó el pago
     *
     * @ORM\ManyToOne(targetEntity="Proyecto", inversedBy="pagos", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $proyecto;

    /**
     * @return float
     */
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * @return mixed
     */
    public function getProveedor()
    {
        return $this->proveedor;
    }

    /**
     * @return mixed
     */
    public function getProyecto()
    {
        return $this->proyecto;
    }

    public function setProveedor(Proveedor $proveedor)
    {
        $proveedor->addPago($this);
        $this->proveedor = $proveedor;
    }

    public function setProyecto(Proyecto $proyecto)
    {
        $proyecto->addPago($this);
        $this->proyecto = $proyecto;
    }
}
