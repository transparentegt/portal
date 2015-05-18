<?php
namespace Transparente\Model;

use Doctrine\ORM\EntityRepository;
use Transparente\Model\Entity\AbstractDoctrineEntity;

abstract class AbstractModel extends EntityRepository
{

    function convert($size)
    {
        $unit = array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }

    /**
     * Guardar una entidad
     *
     * @param AbstractDoctrineEntity $entity
     */
    public function save(AbstractDoctrineEntity $entity)
    {
        $start = $end = 0 ;
        ScraperModel::profileTime($start, $end);
        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();
        $em->clear();
        echo sprintf("\tDB time: %s", ScraperModel::profileTime($start, $end));
    }

    /**
     * Guardar una entidad
     *
     * @param AbstractDoctrineEntity $entity
     */
    public function update(AbstractDoctrineEntity $entity)
    {
        $start = $end = 0 ;
        ScraperModel::profileTime($start, $end);
        $em = $this->getEntityManager();
        $em->merge($entity);
        $em->flush();
        $em->clear();
        echo sprintf("\tDB time: %s", ScraperModel::profileTime($start, $end));
    }

    public function findCount()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery("SELECT count(t.id)  FROM {$this->_entityName} t");
        $count = $query->getSingleScalarResult();
        return $count;
    }


    /**
     * Generates a Zend Paginator based on a DQL
     *
     * @param string $dql   DQL para generar el paginador
     * @param int    $limit Cuantos elementos mostramos por página
     *
     * @return \Zend\Paginator\Paginator
     */
    final protected function getPaginatorFromDql($dql, $limit = 20)
    {
        $query     = $this->getEntityManager()->createQuery($dql);
        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($query);
        $adapter   = new \DoctrineORMModule\Paginator\Adapter\DoctrinePaginator($paginator);
        $paginator = new \Zend\Paginator\Paginator($adapter);
        $page      = (! empty($_GET['page'])) ? $_GET['page'] : 1;
        $paginator->setCurrentPageNumber($page)->setItemCountPerPage($limit);
        return $paginator;
    }
}