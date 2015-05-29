<?php
namespace Transparente\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\Query\ResultSetMapping;

class Index extends AbstractActionController
{
    public function indexAction()
    {
        $db = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        /* @var $db \Doctrine\ORM\EntityManager */
        $sql  = '   SELECT "domicilios de proveedores" as entidad, COUNT(t.id) as total FROM domicilios t
                UNION
                    SELECT "empleados municipales" as entidad,  COUNT(t.id) as total FROM empleado_municipal t
                UNION
                    SELECT "pagos realizados" as entidad,  COUNT(t.id) as total FROM pago t
                UNION
                    SELECT "proveedores" as entidad,  COUNT(t.id) as total FROM proveedor t
                UNION
                    SELECT "representantes legales" as entidad,  COUNT(t.id) as total FROM rep_legal t
                ';
        $rsm  = new ResultSetMapping($db);
        $rsm->addScalarResult('entidad', 'entidad');
        $rsm->addScalarResult('total',   'total');
        $totals = $db->createNativeQuery($sql, $rsm)->getResult();
        return new ViewModel(compact('totals'));
    }
}