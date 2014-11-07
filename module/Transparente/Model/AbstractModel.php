<?php
namespace Transparente\Model;

use Doctrine\ORM\EntityRepository;
use Transparente\Model\Entity\AbstractDoctrineEntity;

abstract class AbstractModel extends EntityRepository
{
    /**
     * Guardar una entidad
     *
     * @param AbstractDoctrineEntity $entity
     */
    public function save(AbstractDoctrineEntity $entity)
    {
        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();
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