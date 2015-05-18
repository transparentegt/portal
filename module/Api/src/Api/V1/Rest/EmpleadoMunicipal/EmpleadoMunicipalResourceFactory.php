<?php
namespace Api\V1\Rest\EmpleadoMunicipal;

class EmpleadoMunicipalResourceFactory
{
    public function __invoke($services)
    {
        return new EmpleadoMunicipalResource($services);
    }
}
