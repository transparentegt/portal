<?php
namespace Api\V1\Rest\RepresentanteLegal;

class RepresentanteLegalResourceFactory
{
    public function __invoke($services)
    {
        return new RepresentanteLegalResource($services);
    }
}
