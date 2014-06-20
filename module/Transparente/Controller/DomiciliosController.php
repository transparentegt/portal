<?php
namespace Transparente\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Transparente\Model\Domicilios;
use Transparente\Form\DomiciliosForm;

class DomiciliosController extends AbstractActionController
{

    protected $domiciliosTable;

    public function indexAction ()
    {
        $paginator = $this->getDomiciliosTable()->fetchAll(true);
        $paginator->setCurrentPageNumber(
                (int) $this->params()
                    ->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);

        return new ViewModel(
                array(
                        'paginator' => $paginator,
                        'db' => $this->getServiceLocator()->get(
                                'Zend\Db\Adapter\Adapter')
                ));
    }

    public function addAction ()
    {
        $form = new DomiciliosForm(
                $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        $form->get('submit')->setValue('Agregar');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $domicilios = new Domicilios();
            $form->setInputFilter($domicilios->getInputFilter());
            $post = array_merge_recursive($request->getPost()->toArray(),
                    $request->getFiles()->toArray());
            $form->setData($post);

            if ($form->isValid()) {
                $domicilios->exchangeArray($form->getData());
                $this->getDomiciliosTable()->saveDomicilios($domicilios);

                return $this->redirect()->toUrl('/transparente/domicilios');
            }
        }
        return array(
                'form' => $form
        );
    }

    public function editAction ()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (! $id) {
            return $this->redirect()->toUrl('/transparente/domicilios/add');
        }
        $domicilios = $this->getDomiciliosTable()->getDomicilios($id);

        $form = new DomiciliosForm(
                $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        $form->bind($domicilios);
        $form->get('submit')->setAttribute('value', 'Guardar');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($domicilios->getInputFilter());
            $post = array_merge_recursive($request->getPost()->toArray(),
                    $request->getFiles()->toArray());
            $form->setData($post);
            if ($form->isValid()) {
                $this->getDomiciliosTable()->saveDomicilios($form->getData());

                return $this->redirect()->toUrl('/transparente/domicilios');
            }
        }

        $sm = $this->getServiceLocator();

        return array(
                'id' => $id,
                'form' => $form,
                'sm' => $sm
        );
    }

    public function deleteAction ()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (! $id) {
            return $this->redirect()->toUrl('/transparente/domicilios/add');
        }
        $this->getDomiciliosTable()->deleteDomicilios($id);
        return $this->redirect()->toUrl('/transparente/domicilios');
    }

    public function getDomiciliosTable ()
    {
        if (! $this->domiciliosTable) {
            $sm = $this->getServiceLocator();
            $this->domiciliosTable = $sm->get('Transparente\Model\DomiciliosTable');
        }
        return $this->domiciliosTable;
    }
}