<?php
namespace geoMunicipios;

use geoMunicipios\Model\geoMunicipios;
use geoMunicipios\Model\geoMunicipiosTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

 class Module
 {
     public function getAutoloaderConfig()
     {
         return array(
             'Zend\Loader\ClassMapAutoloader' => array(
                 __DIR__ . '/autoload_classmap.php',
             ),
             'Zend\Loader\StandardAutoloader' => array(
                 'namespaces' => array(
                     __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                 ),
             ),
         );
     }

     public function getConfig()
     {
         return include __DIR__ . '/config/module.config.php';
     }

     public function getServiceConfig()
     {
         return array(
             'factories' => array(
                 'geoMunicipios\Model\geoMunicipiosTable' =>  function($sm) {
                     $tableGateway = $sm->get('geoMunicipiosTableGateway');
                     $table = new geoMunicipiosTable($tableGateway);
                     return $table;
                 },
                 'geoMunicipiosTableGateway' => function ($sm) {
                     $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                     $resultSetPrototype = new ResultSet();
                     $resultSetPrototype->setArrayObjectPrototype(new geoMunicipios());
                     return new TableGateway('geo_municipios', $dbAdapter, null, $resultSetPrototype);
                 },
             ),
         );
     }     
     
}
 
 ?>