<?php
namespace Transparente\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use DoctrineModule\Paginator\Adapter\Collection as Adapter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;


use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;

class RepresentanteLegalController extends AbstractActionController
{
    /**
     * Listar representantes legales
     *
     * @todo paginar
     * @todo ordenar por columna seleccionada
     */
    public function indexAction()
    {

        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $modelo = $entityManager->getRepository('Transparente\Model\Entity\RepresentanteLegal');


        $query = $modelo->createQueryBuilder('RepresentanteLegal');
        $adapter = new DoctrineAdapter(new ORMPaginator($query));
        $paginator = new Paginator($adapter);
        $paginator->setDefaultItemCountPerPage(20);

        $page = (int)$this->params()->fromQuery('page');
        if($page) $paginator->setCurrentPageNumber($page);


        return new ViewModel(compact('paginator'));

    }

    /**
     * Ver los detalles de un representante legal
     */
    public function detallesAction()
    {
        $id          = $this->params('id');
        $entityModel = $this->getServiceLocator()->get('Transparente\Model\RepresentanteLegalModel');
        $entity      = $entityModel->find($id);
        return new ViewModel(compact('entity'));
    }

    public function nombresComercialesAction()
    {
        $modelo    = $this->getServiceLocator()->get('Transparente\Model\RepresentanteLegalModel');
        $entidades = $modelo->findByNombresComerciales();
        $paginator        = new Paginator(new Adapter(new ArrayCollection($entidades)));
        if (!empty($_GET['page'])) {
            $paginator->setCurrentPageNumber($_GET['page']);
        }
        return new ViewModel(compact('paginator'));
    }

    public function multiProveedorAction()
    {
        $modelo    = $this->getServiceLocator()->get('Transparente\Model\RepresentanteLegalModel');
        $entidades = $modelo->findByMultiProveedor();
        $paginator        = new Paginator(new Adapter(new ArrayCollection($entidades)));
        if (!empty($_GET['page'])) {
            $paginator->setCurrentPageNumber($_GET['page']);
        }
        return new ViewModel(compact('paginator'));
    }

    public function multiLevelAction()
    {
        $modelo    = $this->getServiceLocator()->get('Transparente\Model\RepresentanteLegalModel');
        $entidades = $modelo->findByMultiLevel();
        return new ViewModel(compact('entidades'));
    }
}