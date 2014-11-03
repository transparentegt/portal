<?php
namespace Api\V1\Rest\Proyecto;

class ProyectoResourceFactory
{
    public function __invoke($services)
    {
        return new ProyectoResource($services);
    }
}
