<?php

return array(
     'controllers' => array(
         'invokables' => array(
             'geoDepartamentos\Controller\geoDepartamentos' => 'geoDepartamentos\Controller\geoDepartamentosController',
         ),
     ),
     'router' => array(
         'routes' => array(
             'geoDepartamentos' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/geoDepartamentos[/][:action][/:id]',
                     'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                         'id'     => '[0-9]+',
                     ),
                     'defaults' => array(
                         'controller' => 'geoDepartamentos\Controller\geoDepartamentos',
                         'action'     => 'index',
                     ),
                 ),
             ),
         ),
     ),
    'view_manager' => array(
         'template_path_stack' => array(
             'geoDepartamentos' => __DIR__ . '/../view',
         ),
     ),
 );

?>