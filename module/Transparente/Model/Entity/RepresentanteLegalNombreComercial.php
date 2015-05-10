<?php
namespace Transparente\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Transparente\Model\Entity\AbstractDoctrineEntity;
use Transparente\Model\ScraperModel;

/**
 * @ORM\Entity
 * @ORM\Table(name="rep_legal_nombre_comercial")
 */
class RepresentanteLegalNombreComercial extends AbstractDoctrineEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="RepresentanteLegal", inversedBy="nombres_comerciales")
     * @ORM\JoinColumn(name="id_rep_legal", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $representante_legal;

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

    public function setRepresentanteLegal(RepresentanteLegal $representante_legal)
    {
        $this->representante_legal = $representante_legal;
    }

}