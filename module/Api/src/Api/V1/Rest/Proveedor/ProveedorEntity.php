<?php
namespace Api\V1\Rest\Proveedor;

use Transparente\Model\ScraperModel;

class ProveedorEntity
{
    /**
     *
     * @var \Transparente\Model\Entity\Proveedor
     */
    private  $entity;

    public function __construct(\Transparente\Model\Entity\Proveedor $entity)
    {
        $this->entity = $entity;
    }

    public function getArrayCopy()
    {
        $proyectos = [];
        foreach ($this->entity->getPagos() as $pago) {
            $proyectos[] =  [
                'id'     => $pago->getProyecto()->getId(),
                'nombre' => $pago->getProyecto()->getNombre(),
                'monto'  => $pago->getMonto(),
                'link'   => ScraperModel::BASE_URL . '/api/proyecto/' . $pago->getProyecto()->getId(),
            ];
        }

        return [
                'id'                             => $this->entity->getId(),
                'email'                          => $this->entity->getEmail(),
                'inscripción_fecha_constitución' => $this->entity->getInscripciónFechaConstitución()->format('Y-m-d'),
                'inscripción_fecha_definitiva'   => $this->entity->getInscripciónFechaDefinitiva()->format('Y-m-d'),
                'inscripción_fecha_provisional'  => $this->entity->getInscripciónFechaProvisional()->format('Y-m-d'),
                'inscripción_fecha_sat'          => $this->entity->getInscripciónFechaSat()->format('Y-m-d'),
                'nit'                            => $this->entity->getNit(),
                'nombre'                         => $this->entity->getNombre(),
                'principal_actividad'            => $this->entity->getPrincipalActividad(),
                'principal_trabajo'              => $this->entity->getPrincipalTrabajo(),
                'status'                         => $this->entity->getStatus(true),
                'proyectos'                      => $proyectos,
        ];
    }
}
