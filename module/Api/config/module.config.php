<?php
return array(
    'router' => array(
        'routes' => array(
            'api.rest.proveedor' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/proveedor[/:nit]',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\Proveedor\\Controller',
                    ),
                ),
            ),
        ),
    ),
    'zf-versioning' => array(
        'uri' => array(
            0 => 'api.rest.proveedor',
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'Api\\V1\\Rest\\Proveedor\\ProveedorResource' => 'Api\\V1\\Rest\\Proveedor\\ProveedorResourceFactory',
        ),
    ),
    'zf-rest' => array(
        'Api\\V1\\Rest\\Proveedor\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\Proveedor\\ProveedorResource',
            'route_name' => 'api.rest.proveedor',
            'route_identifier_name' => 'nit',
            'collection_name' => 'proveedor',
            'entity_http_methods' => array(
                0 => 'GET',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'Api\\V1\\Rest\\Proveedor\\ProveedorEntity',
            'collection_class' => 'Api\\V1\\Rest\\Proveedor\\ProveedorCollection',
            'service_name' => 'Proveedor',
        ),
    ),
    'zf-content-negotiation' => array(
        'controllers' => array(
            'Api\\V1\\Rest\\Proveedor\\Controller' => 'HalJson',
        ),
        'accept_whitelist' => array(
            'Api\\V1\\Rest\\Proveedor\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
        ),
        'content_type_whitelist' => array(
            'Api\\V1\\Rest\\Proveedor\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ),
        ),
    ),
    'zf-hal' => array(
        'metadata_map' => array(
            'Api\\V1\\Rest\\Proveedor\\ProveedorEntity' => array(
                'entity_identifier_name' => 'nit',
                'route_name' => 'api.rest.proveedor',
                'route_identifier_name' => 'nit',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\Proveedor\\ProveedorCollection' => array(
                'entity_identifier_name' => 'nit',
                'route_name' => 'api.rest.proveedor',
                'route_identifier_name' => 'nit',
                'is_collection' => true,
            ),
        ),
    ),
    'zf-content-validation' => array(
        'Api\\V1\\Rest\\Proveedor\\Controller' => array(
            'input_filter' => 'Api\\V1\\Rest\\Proveedor\\Validator',
        ),
    ),
    'input_filter_specs' => array(
        'Api\\V1\\Rest\\Proveedor\\Validator' => array(
            0 => array(
                'name' => 'id',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Identificador interno en la base de datos',
                'allow_empty' => false,
                'continue_if_empty' => false,
            ),
            1 => array(
                'name' => 'email',
                'required' => false,
                'filters' => array(),
                'validators' => array(),
                'description' => 'dirección del correo electrónico del proveedor, muchos no tienen este campo',
            ),
            2 => array(
                'name' => 'inscripción_fecha_constitución',
                'required' => false,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Fecha de constitución',
            ),
            3 => array(
                'name' => 'inscripción_fecha_definitiva',
                'required' => false,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Inscripción DEFINITIVA en el Registro Mercantil',
            ),
            4 => array(
                'name' => 'inscripción_fecha_provisional',
                'required' => false,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Inscripción PROVISIONAL en el Registro Mercantil',
            ),
            5 => array(
                'name' => 'inscripción_fecha_sat',
                'required' => false,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Inscripción en la SAT',
            ),
            6 => array(
                'name' => 'nit',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Número de Identificación Tributaria (NIT)',
            ),
            7 => array(
                'name' => 'nombre',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Nombre o razón social',
            ),
            8 => array(
                'name' => 'principal_actividad',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Actividad principal',
            ),
            9 => array(
                'name' => 'principal_trabajo',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Principales trabajos realizados',
            ),
            10 => array(
                'name' => 'status',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
            ),
        ),
    ),
);
