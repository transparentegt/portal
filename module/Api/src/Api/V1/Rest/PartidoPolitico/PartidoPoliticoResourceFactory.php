<?php
namespace Api\V1\Rest\PartidoPolitico;

class PartidoPoliticoResourceFactory
{
    public function __invoke($services)
    {
        return new PartidoPoliticoResource($services);
    }
}
