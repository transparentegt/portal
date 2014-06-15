<?php

return array(
     'controllers' => array(
         'invokables' => array(
             'domicilios\Controller\domicilios' => 'domicilios\Controller\domiciliosController',
         ),
     ),
     'router' => array(
         'routes' => array(
             'domicilios' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/domicilios[/][:action][/:id]',
                     'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                         'id'     => '[0-9]+',
                     ),
                     'defaults' => array(
                         'controller' => 'domicilios\Controller\domicilios',
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