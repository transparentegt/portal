<?php
/**
 * Configuración del módulo de Transparente
 *
 * @todo simplificar más este archivo (la documentación para un módulo mínimo es muy poca)
 * @todo el application_entities debería de ser por namespace, ahy que probar si funciona usando __NAMESPACE__
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
            'Transparente\Controller\Index'              => 'Transparente\Controller\Index',
            'Transparente\Controller\Proveedores'        => 'Transparente\Controller\ProveedoresController',
            'Transparente\Controller\Scraper'            => 'Transparente\Controller\ScraperController',
            'Transparente\Controller\RepresentanteLegal' => 'Transparente\Controller\RepresentanteLegalController',
       ),
    ),
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Transparente\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
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
            // ['route' => 'home', 'label' => 'Guatemala Transparente'],
            ['route' => 'transparente/default',     'controller' => 'proveedores', 'label' => 'proveedores', 'pages' => [
                ['route' => 'transparente/default', 'controller' => 'proveedores', 'action' => 'index',     'label' => 'listar todos'],
                ['route' => 'transparente/default', 'controller' => 'proveedores', 'action' => 'no-fiscal', 'label' => 'sin domicilio fiscal']
            ]],
            ['route' => 'transparente/default',     'controller' => 'representante-legal', 'label' => 'representantes legales', 'pages' => [
                ['route' => 'transparente/default', 'controller' => 'representante-legal', 'action' => 'index',               'label' => 'listar todos'],
                ['route' => 'transparente/default', 'controller' => 'representante-legal', 'action' => 'multi-proveedor',     'label' => 'con más de 1 empresa proveedora'],
                ['route' => 'transparente/default', 'controller' => 'representante-legal', 'action' => 'nombres-comerciales', 'label' => 'con nombres comerciales'],
                ['route' => 'transparente/default', 'controller' => 'representante-legal', 'action' => 'multi-level',         'label' => 'con representantes legales'],
            ]],
            ['route' => 'transparente/default', 'controller' => 'index', 'action' => 'about',  'label' => 'info'],
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
            ),
        ),
    ),
);
