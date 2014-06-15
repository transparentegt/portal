<?php
namespace geoDepartamentos;

use geoDepartamentos\Model\geoDepartamentos;
use geoDepartamentos\Model\geoDepartamentosTable;
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
                 'geoDepartamentos\Model\geoDepartamentosTable' =>  function($sm) {
                     $tableGateway = $sm->get('geoDepartamentosTableGateway');
                     $table = new geoDepartamentosTable($tableGateway);
                     return $table;
                 },
                 'geoDepartamentosTableGateway' => function ($sm) {
                     $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                     $resultSetPrototype = new ResultSet();
                     $resultSetPrototype->setArrayObjectPrototype(new geoDepartamentos());
                     return new TableGateway('geo_departamentos', $dbAdapter, null, $resultSetPrototype);
                 },
             ),
         );
     }     
     
}
 
 ?>