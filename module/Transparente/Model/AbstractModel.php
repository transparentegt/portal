<?php
namespace Transparente\Model;

use Doctrine\ORM\EntityRepository;
use Transparente\Model\Entity\AbstractDoctrineEntity;

abstract class AbstractModel extends EntityRepository
{

    public function __destruct()
    {
        $this->flush();
    }

    function convert($size)
    {
        $unit = array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }

    public function flush()
    {
        $em = $this->getEntityManager();
        $em->flush();
        $em->clear();
    }

    /**
     * Guardar una entidad
     *
     * @param AbstractDoctrineEntity $entity
     */
    public function save(AbstractDoctrineEntity $entity)
    {
        $start = $end = 0 ;
        $mem = memory_get_usage();
        ScraperModel::profileTime($start, $end);
        $em = $this->getEntityManager();
        $em->persist($entity);
        echo sprintf("\tDB time: %s", ScraperModel::profileTime($start, $end));
        echo sprintf("\tmem: %s", memory_get_usage() - $mem);
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