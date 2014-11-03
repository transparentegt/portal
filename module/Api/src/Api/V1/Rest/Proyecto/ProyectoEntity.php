<?php
namespace Api\V1\Rest\Proyecto;

use Transparente\Model\ScraperModel;

class ProyectoEntity
{
    /**
     *
     * @var \Transparente\Model\Entity\Proyecto
     */
    private  $entity;

    public function __construct(\Transparente\Model\Entity\Proyecto $entity)
    {
        $this->entity = $entity;
    }

    public function getArrayCopy()
    {
        $proveedores = [];
        foreach ($this->entity->getPagos() as $pago) {
            $proveedores[] = [
                'id'     => $pago->getProveedor()->getId(),
                'nombre' => $pago->getProveedor()->getNombre(),
                'monto'  => $pago->getMonto(),
                'link'   => ScraperModel::BASE_URL . '/api/proveedor/' . $pago->getProveedor()->getNit(),
            ];
        }
        return [
                'id'                         => $this->entity->getId(),
                'categoría'                  => $this->entity->getCategoría(),
                'entidad_compradora'         => $this->entity->getEntidadCompradora(),
                'entidad_compradora_tipo'    => $this->entity->getEntidadCompradoraTipo(),
                'entidad_compradora_unidad'  => $this->entity->getEntidadCompradoraUnidad(),
                'fecha_cierre_ofertas'       => $this->entity->getFechaCierreOfertas()->format('Y-m-d'),
                'fecha_finalización'         => ($this->entity->getFechaFinalización())        ? $this->entity->getFechaFinalización()->format('Y-m-d')        : NULL,
                'fecha_presentación_ofertas' => ($this->entity->getFechaPresentaciónOfertas()) ? $this->entity->getFechaPresentaciónOfertas()->format('Y-m-d') : NULL,
                'fecha_publicación'          => ($this->entity->getFechaPublicación())         ? $this->entity->getFechaPublicación()->format('Y-m-d')         : NULL,
                'modalidad'                  => $this->entity->getModalidad(),
                'nombre'                     => $this->entity->getNombre(),
                'status'                     => $this->entity->getStatus(),
                'tipo'                       => $this->entity->getTipo(),
                'tipo_recepción_oferta'      => $this->entity->getTipoRecepciónOferta(),
                'proveedores'                => $proveedores,
        ];
    }
}
