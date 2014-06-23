<?php
/**
 *
 * @todo Agregar vendor namespace
 */
namespace Transparente;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

// ABCD
use Transparente\Model\Domicilio;
use Transparente\Model\DomiciliosTable;
use Transparente\Model\geoDepartamentos;
use Transparente\Model\geoDepartamentosTable;
use Transparente\Model\geoMunicipios;
use Transparente\Model\geoMunicipiosTable;
use Transparente\Model\Proveedor;
use Transparente\Model\ProveedoresTable;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
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
                    __NAMESPACE__ => __DIR__
                )
            )
        );
    }

    /**
     * Define las factories para crear los modelos y tablas para conectar a la DB
     *
     * @todo Los factories se podrían definir en la AbstractDbTable
     *
     * @return array
     */
    public function getServiceConfig()
    {
        return array(
            'factories' => [
                // Domicilios
                'Transparente\Model\DomiciliosTable' => function ($serviceManager) {
                    $tableGateway = $serviceManager->get('DomiciliosTableGateway');
                    $table        = new DomiciliosTable($tableGateway);
                    return $table;
                },
                'DomiciliosTableGateway' => function ($serviceManager) {
                    $dbAdapter          = $serviceManager->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Domicilio());
                    return new TableGateway('domicilios', $dbAdapter, null, $resultSetPrototype);
                },

                'Transparente\Model\geoDepartamentosTable' => function ($sm) {
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
                'Transparente\Model\geoMunicipiosTable' => function ($sm) {
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
                // Proveedores
                'Transparente\Model\ProveedoresTable' => function ($serviceManager) {
                    $tableGateway = $serviceManager->get('ProveedoresTableGateway');
                    $table        = new ProveedoresTable($tableGateway);
                    return $table;
                },
                'ProveedoresTableGateway' => function ($serviceManager) {
                    $dbAdapter          = $serviceManager->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Proveedor());
                    return new TableGateway('proveedores', $dbAdapter, null, $resultSetPrototype);
                },
            ]
        );
    }
}
