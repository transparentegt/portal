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
            'api.rest.proyecto' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/proyecto[/:proyecto_id]',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\Proyecto\\Controller',
                    ),
                ),
            ),
            'api.rest.empleado-municipal' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/empleado-municipal',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\EmpleadoMunicipal\\Controller',
                    ),
                ),
            ),
            'api.rest.partido-politico' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/partido-politico[/:partido_politico_id]',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\PartidoPolitico\\Controller',
                    ),
                ),
            ),
            'api.rest.representante-legal' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/representante-legal[/:representante_legal_id]',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\RepresentanteLegal\\Controller',
                    ),
                ),
            ),
        ),
    ),
    'zf-versioning' => array(
        'uri' => array(
            0 => 'api.rest.proveedor',
            1 => 'api.rest.proyecto',
            2 => 'api.rest.empleado-municipal',
            3 => 'api.rest.partido-politico',
            4 => 'api.rest.representante-legal',
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'Api\\V1\\Rest\\Proveedor\\ProveedorResource' => 'Api\\V1\\Rest\\Proveedor\\ProveedorResourceFactory',
            'Api\\V1\\Rest\\Proyecto\\ProyectoResource' => 'Api\\V1\\Rest\\Proyecto\\ProyectoResourceFactory',
            'Api\\V1\\Rest\\EmpleadoMunicipal\\EmpleadoMunicipalResource' => 'Api\\V1\\Rest\\EmpleadoMunicipal\\EmpleadoMunicipalResourceFactory',
            'Api\\V1\\Rest\\PartidoPolitico\\PartidoPoliticoResource' => 'Api\\V1\\Rest\\PartidoPolitico\\PartidoPoliticoResourceFactory',
            'Api\\V1\\Rest\\RepresentanteLegal\\RepresentanteLegalResource' => 'Api\\V1\\Rest\\RepresentanteLegal\\RepresentanteLegalResourceFactory',
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
            'collection_query_whitelist' => array(
                0 => 'sort',
                1 => 'order',
                2 => 'where',
                3 => 'filter',
            ),
            'page_size' => '10',
            'page_size_param' => 'size',
            'entity_class' => 'Api\\V1\\Rest\\Proveedor\\ProveedorEntity',
            'collection_class' => 'Api\\V1\\Rest\\Proveedor\\ProveedorCollection',
            'service_name' => 'Proveedor',
        ),
        'Api\\V1\\Rest\\Proyecto\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\Proyecto\\ProyectoResource',
            'route_name' => 'api.rest.proyecto',
            'route_identifier_name' => 'proyecto_id',
            'collection_name' => 'proyecto',
            'entity_http_methods' => array(
                0 => 'GET',
            ),
            'collection_http_methods' => array(),
            'collection_query_whitelist' => array(),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'Api\\V1\\Rest\\Proyecto\\ProyectoEntity',
            'collection_class' => 'Api\\V1\\Rest\\Proyecto\\ProyectoCollection',
            'service_name' => 'Proyecto',
        ),
        'Api\\V1\\Rest\\EmpleadoMunicipal\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\EmpleadoMunicipal\\EmpleadoMunicipalResource',
            'route_name' => 'api.rest.empleado-municipal',
            'route_identifier_name' => 'empleado_municipal_id',
            'collection_name' => 'list',
            'entity_http_methods' => array(),
            'collection_http_methods' => array(
                0 => 'GET',
            ),
            'collection_query_whitelist' => array(
                0 => 'sort',
                1 => 'order',
                2 => 'where',
                3 => 'filter',
            ),
            'page_size' => '10',
            'page_size_param' => 'size',
            'entity_class' => 'Api\\V1\\Rest\\EmpleadoMunicipal\\EmpleadoMunicipalEntity',
            'collection_class' => 'Api\\V1\\Rest\\EmpleadoMunicipal\\EmpleadoMunicipalCollection',
            'service_name' => 'EmpleadoMunicipal',
        ),
        'Api\\V1\\Rest\\PartidoPolitico\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\PartidoPolitico\\PartidoPoliticoResource',
            'route_name' => 'api.rest.partido-politico',
            'route_identifier_name' => 'partido_politico_id',
            'collection_name' => 'partido_politico',
            'entity_http_methods' => array(
                0 => 'GET',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
            ),
            'collection_query_whitelist' => array(
                0 => 'filter',
                1 => 'sort',
                2 => 'where',
                3 => 'order',
            ),
            'page_size' => '10',
            'page_size_param' => 'size',
            'entity_class' => 'Api\\V1\\Rest\\PartidoPolitico\\PartidoPoliticoEntity',
            'collection_class' => 'Api\\V1\\Rest\\PartidoPolitico\\PartidoPoliticoCollection',
            'service_name' => 'PartidoPolitico',
        ),
        'Api\\V1\\Rest\\RepresentanteLegal\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\RepresentanteLegal\\RepresentanteLegalResource',
            'route_name' => 'api.rest.representante-legal',
            'route_identifier_name' => 'representante_legal_id',
            'collection_name' => 'representante_legal',
            'entity_http_methods' => array(
                0 => 'GET',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
            ),
            'collection_query_whitelist' => array(
                0 => 'filter',
                1 => 'sort',
                2 => 'order',
                3 => 'where',
            ),
            'page_size' => 25,
            'page_size_param' => 'size',
            'entity_class' => 'Api\\V1\\Rest\\RepresentanteLegal\\RepresentanteLegalEntity',
            'collection_class' => 'Api\\V1\\Rest\\RepresentanteLegal\\RepresentanteLegalCollection',
            'service_name' => 'RepresentanteLegal',
        ),
    ),
    'zf-content-negotiation' => array(
        'controllers' => array(
            'Api\\V1\\Rest\\Proveedor\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\Proyecto\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\EmpleadoMunicipal\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\PartidoPolitico\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\RepresentanteLegal\\Controller' => 'HalJson',
        ),
        'accept_whitelist' => array(
            'Api\\V1\\Rest\\Proveedor\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Api\\V1\\Rest\\Proyecto\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Api\\V1\\Rest\\EmpleadoMunicipal\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Api\\V1\\Rest\\PartidoPolitico\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Api\\V1\\Rest\\RepresentanteLegal\\Controller' => array(
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
            'Api\\V1\\Rest\\Proyecto\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ),
            'Api\\V1\\Rest\\EmpleadoMunicipal\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ),
            'Api\\V1\\Rest\\PartidoPolitico\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ),
            'Api\\V1\\Rest\\RepresentanteLegal\\Controller' => array(
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
            'Api\\V1\\Rest\\Proyecto\\ProyectoEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'api.rest.proyecto',
                'route_identifier_name' => 'proyecto_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\Proyecto\\ProyectoCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'api.rest.proyecto',
                'route_identifier_name' => 'proyecto_id',
                'is_collection' => true,
            ),
            'Api\\V1\\Rest\\EmpleadoMunicipal\\EmpleadoMunicipalEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'api.rest.empleado-municipal',
                'route_identifier_name' => 'empleado_municipal_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\EmpleadoMunicipal\\EmpleadoMunicipalCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'api.rest.empleado-municipal',
                'route_identifier_name' => 'empleado_municipal_id',
                'is_collection' => true,
            ),
            'Api\\V1\\Rest\\PartidoPolitico\\PartidoPoliticoEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'api.rest.partido-politico',
                'route_identifier_name' => 'partido_politico_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\PartidoPolitico\\PartidoPoliticoCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'api.rest.partido-politico',
                'route_identifier_name' => 'partido_politico_id',
                'is_collection' => true,
            ),
            'Api\\V1\\Rest\\RepresentanteLegal\\RepresentanteLegalEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'api.rest.representante-legal',
                'route_identifier_name' => 'representante_legal_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\RepresentanteLegal\\RepresentanteLegalCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'api.rest.representante-legal',
                'route_identifier_name' => 'representante_legal_id',
                'is_collection' => true,
            ),
        ),
    ),
    'zf-content-validation' => array(
        'Api\\V1\\Rest\\Proveedor\\Controller' => array(
            'input_filter' => 'Api\\V1\\Rest\\Proveedor\\Validator',
        ),
        'Api\\V1\\Rest\\Proyecto\\Controller' => array(
            'input_filter' => 'Api\\V1\\Rest\\Proyecto\\Validator',
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
        'Api\\V1\\Rest\\Proyecto\\Validator' => array(
            0 => array(
                'name' => 'id',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Número de identificación del proyecto / Número de Operación Guatecompras (NOG)',
            ),
            1 => array(
                'name' => 'categoría',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Categoría',
            ),
            2 => array(
                'name' => 'entidad_compradora',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Entidad compradora',
            ),
            3 => array(
                'name' => 'entidad_compradora_tipo',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Tipo de entidad',
            ),
            4 => array(
                'name' => 'entidad_compradora_unidad',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Unidad compradora',
            ),
            5 => array(
                'name' => 'fecha_cierre_ofertas',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Fecha de cierre de recepción de ofertas:',
            ),
            6 => array(
                'name' => 'fecha_finalización',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Fecha de finalización',
            ),
            7 => array(
                'name' => 'fecha_presentación_ofertas',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Fecha de presentación de ofertas',
            ),
            8 => array(
                'name' => 'fecha_publicación',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Fecha de publicación',
            ),
            9 => array(
                'name' => 'modalidad',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => '[--No Especificado--], Compra directa, Compra directa por ausencia de ofertas, Contrato Abierto, Cotización, Excepción y otros procedimientos, Licitación pública, Transparencia en el uso de fondos públicos y otros contratos (Art. 54)',
            ),
            10 => array(
                'name' => 'nombre',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Nombre o descrpción del proyecto',
            ),
            11 => array(
                'name' => 'status',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Finalizado adjudicado cedido, Finalizado anulado (prescindido), Terminado adjudicado, Terminado Rematado',
            ),
            12 => array(
                'name' => 'tipo',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => '(Vinculación referente a Bienes y Servicios)., (Vinculación referente a infraestructura)., Público, Restringido (solamente pueden participar los proveedores previamente invitados por el comprador)',
            ),
            13 => array(
                'name' => 'tipo_recepción_oferta',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'NULL, Solo electrónicas, Solo en papel.',
            ),
        ),
    ),
);
