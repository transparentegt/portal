<?php
/**
 * Configuración del módulo de Transparente
 *
 * @todo simplificar más este archivo (la documentación para un módulo mínimo es muy poca)
 * @todo el application_entities debería de ser por namespace, hay que probar si funciona usando __NAMESPACE__
 * @todo las rutas deberían de poder definirse desde el controlador, ver https://github.com/str/gtt/issues/48
 */
return array(
    'doctrine' => array(
      'driver' => array(
        'application_entities' => array(
          'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
          'cache' => 'array',
          'paths' => array(__DIR__ . '/Model/Entity')
        ),

        'orm_default' => array(
          'drivers' => array(
            'Transparente\Model\Entity' => 'application_entities'
          )
    ))),

    'controllers' => array(
        'invokables' => array(
            'Transparente\Controller\Domicilios'         => 'Transparente\Controller\DomiciliosController',
            'Transparente\Controller\EmpleadoMunicipal'  => 'Transparente\Controller\EmpleadoMunicipalController',
            'Transparente\Controller\Index'              => 'Transparente\Controller\Index',
            'Transparente\Controller\Municipio'          => 'Transparente\Controller\MunicipioController',
            'Transparente\Controller\PartidoPolitico'    => 'Transparente\Controller\PartidoPoliticoController',
            'Transparente\Controller\Proveedores'        => 'Transparente\Controller\ProveedoresController',
            'Transparente\Controller\Proyecto'           => 'Transparente\Controller\ProyectoController',
            'Transparente\Controller\RepresentanteLegal' => 'Transparente\Controller\RepresentanteLegalController',
            'Transparente\Controller\Scraper'            => 'Transparente\Controller\ScraperController',
       ),
    ),
    'router' => array(
        'routes' => array(
            'home' => [
                'type'    => 'Literal',
                'options' => ['defaults' => [
                    'controller' => 'Transparente\Controller\Index',
                    'action'     => 'index',
                ], 'route' => '/']
            ],
            'about' => [
                'type'    => 'Literal',
                'options' => ['defaults' => [
                    'controller' => 'PhlySimplePage\Controller\Page',
                    'template'   => 'page/about',
                ], 'route' => '/about']
            ],
            'contact' => [
                'type'    => 'Literal',
                'options' => ['defaults' => [
                    'controller' => 'Transparente\Controller\Index',
                    'action'     => 'contact',
                ], 'route' => '/contact']
            ],

            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /module/:controller/:action
            'transparente' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/transparente',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Transparente\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action[/:id]]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'navigation' => [
        'default' => [
            ['route' => 'about', 'label' => '<i class="fa fa-info-circle"></i> <span>¿Qué es transparente.gt?</span>'],
            ['route' => 'transparente/default', 'label' => '<i class="fa fa-database"></i> <span>datos abiertos</span>', 'pages' => [
                ['route' => 'transparente/default', 'action' => 'index', 'controller' => 'empleado-municipal',  'label' => 'empleados municipales'],
                ['route' => 'transparente/default', 'action' => 'index', 'controller' => 'municipio',           'label' => 'municipalidades'],
                // ['route' => 'transparente/default', 'action' => 'index', 'controller' => 'partido-politico',    'label' => 'partidos políticos'],
                ['route' => 'transparente/default', 'action' => 'index', 'controller' => 'proveedores',         'label' => 'proveedores'],
                ['route' => 'transparente/default', 'action' => 'index', 'controller' => 'representante-legal', 'label' => 'representantes legales'],
            ]],
            ['route' => 'transparente/default', 'label' => '<i class="fa fa-folder-open"></i> <span>reportes</span>', 'pages' => [
                ['route' => 'transparente/default', 'controller' => 'proveedores',         'action' => 'no-fiscal',           'label' => 'proveedores sin domicilio fiscal'],
                ['route' => 'transparente/default', 'controller' => 'representante-legal', 'action' => 'multi-proveedor',     'label' => 'representantes de varias empresas'],
                ['route' => 'transparente/default', 'controller' => 'representante-legal', 'action' => 'nombres-comerciales', 'label' => 'representantes como proveedores'],
                ['route' => 'transparente/default', 'controller' => 'representante-legal', 'action' => 'multi-level',         'label' => 'representantes multi niveles'],
            ]],
            ['uri' => ' /apigility/documentation/Api-v1',  'label' => '<i class="fa fa-connectdevelop"></i> <span>API</span>'],
            ['uri' => ' /',  'label' => '<i class="fa fa-bug"></i> <span>reportar un problema</span>'],
            ['route' => 'contact',  'label' => '<i class="fa fa-envelope"></i> <span>contacto</span>'],
        ],
    ],
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),

    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/view/layout/layout.phtml',
            'error/404'               => __DIR__ . '/view/error/404.phtml',
            'error/index'             => __DIR__ . '/view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
                'scraper' => [
                    'options' => [
                        'route'    => 'scraper [--verbose|-v]',
                        'defaults' => [
                            'controller' => 'Transparente\Controller\Scraper',
                            'action'     => 'index'
                        ]
                    ]
                ]
            ),
        ),
    ),
);
