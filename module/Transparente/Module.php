<?php
/**
 *
 * @todo Agregar vendor namespace
 */
namespace Transparente;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;

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
                'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',

                // DomicilioModel
                'Transparente\Model\DomicilioModel' => function (ServiceManager $sm) {
                    $em    = $sm->get('Doctrine\ORM\EntityManager');
                    $model = $em->getRepository('Transparente\Model\Entity\Domicilio');
                    return $model;
                },

                // EmpleadoMunicipalModel
                'Transparente\Model\EmpleadoMunicipalModel' => function (ServiceManager $sm) {
                    $em    = $sm->get('Doctrine\ORM\EntityManager');
                    $model = $em->getRepository('Transparente\Model\Entity\EmpleadoMunicipal');
                    return $model;
                },

                // GeoDepartamentoModel
                'Transparente\Model\GeoDepartamentoModel' => function (ServiceManager $sm) {
                    $em    = $sm->get('Doctrine\ORM\EntityManager');
                    $model = $em->getRepository('Transparente\Model\Entity\GeoDepartamento');
                    return $model;
                },

                // GeoMunicipioModel
                'Transparente\Model\GeoMunicipioModel' => function (ServiceManager $sm) {
                    $em    = $sm->get('Doctrine\ORM\EntityManager');
                    $model = $em->getRepository('Transparente\Model\Entity\GeoMunicipio');
                    return $model;
                },

                // PartidoPoliticoModel
                'Transparente\Model\PartidoPoliticoModel' => function (ServiceManager $sm) {
                    $em    = $sm->get('Doctrine\ORM\EntityManager');
                    $model = $em->getRepository('Transparente\Model\Entity\PartidoPolitico');
                    return $model;
                },

                // ProveedorModel
                'Transparente\Model\ProveedorModel' => function (ServiceManager $sm) {
                    $em    = $sm->get('Doctrine\ORM\EntityManager');
                    $model = $em->getRepository('Transparente\Model\Entity\Proveedor');
                    return $model;
                },

                // ProyectoModel
                'Transparente\Model\ProyectoModel' => function (ServiceManager $sm) {
                    $em    = $sm->get('Doctrine\ORM\EntityManager');
                    $model = $em->getRepository('Transparente\Model\Entity\Proyecto');
                    return $model;
                },

                // RepresentanteLegalModel
                'Transparente\Model\RepresentanteLegalModel' => function (ServiceManager $sm) {
                    $em    = $sm->get('Doctrine\ORM\EntityManager');
                    $model = $em->getRepository('Transparente\Model\Entity\RepresentanteLegal');
                    return $model;
                },

            ]
        );
    }
}
