<?php
namespace Api\V1\Rest\Proveedor;

class ProveedorResourceFactory
{
    public function __invoke($services)
    {
        return new ProveedorResource($services);
    }
}
