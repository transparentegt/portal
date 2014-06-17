<?php
/**
 *
 * @todo Agregar vendor namespace
 */
namespace Transparente;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

//Modulos ABCD
use Transparente\Model\geoDepartamentos;
use Transparente\Model\geoDepartamentosTable;
use Transparente\Model\geoMunicipios;
use Transparente\Model\geoMunicipiosTable;
use Transparente\Model\domicilios;
use Transparente\Model\domiciliosTable;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ ,
                ),
            ),
        );
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
                 'domicilios\Model\domiciliosTable' =>  function($sm) {
                     $tableGateway = $sm->get('domiciliosTableGateway');
                     $table = new domiciliosTable($tableGateway);
                     return $table;
                 },
                 'domiciliosTableGateway' => function ($sm) {
                     $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                     $resultSetPrototype = new ResultSet();
                     $resultSetPrototype->setArrayObjectPrototype(new domicilios());
                     return new TableGateway('domicilios', $dbAdapter, null, $resultSetPrototype);
                 },
             ),
         );
     }     
	
}
