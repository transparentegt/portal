<?php

return array(
     'controllers' => array(
         'invokables' => array(
             'geoMunicipios\Controller\geoMunicipios' => 'geoMunicipios\Controller\geoMunicipiosController',
         ),
     ),
     'router' => array(
         'routes' => array(
             'geoMunicipios' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/geoMunicipios[/][:action][/:id]',
                     'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                         'id'     => '[0-9]+',
                     ),
                     'defaults' => array(
                         'controller' => 'geoMunicipios\Controller\geoMunicipios',
                         'action'     => 'index',
                     ),
                 ),
             ),
         ),
     ),
    'view_manager' => array(
         'template_path_stack' => array(
             'departamentos' => __DIR__ . '/../view',
         ),
     ),
 );

?>