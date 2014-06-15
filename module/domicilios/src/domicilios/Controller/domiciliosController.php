<?php

 namespace domicilios\Controller;

 use Zend\Mvc\Controller\AbstractActionController;
 use Zend\View\Model\ViewModel;
 use domicilios\Model\domicilios;
 use domicilios\Form\domiciliosForm;

 class domiciliosController extends AbstractActionController
 {
     protected $domiciliosTable;	 
	 
    public function indexAction()
    {
	
        $paginator = $this->getdomiciliosTable()->fetchAll(true);
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array(
            'paginator' => $paginator,
            'db' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter')
        ));
    }

    public function addAction()
     {		 
         $form = new domiciliosForm($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
         $form->get('submit')->setValue('Agregar');

         $request = $this->getRequest();
         if ($request->isPost()) {
             $domicilios = new domicilios();
             $form->setInputFilter($domicilios->getInputFilter());
			 $post = array_merge_recursive($request->getPost()->toArray(),
	            $request->getFiles()->toArray()
	        );
             $form->setData($post);
			 
             if ($form->isValid()) {
                 $domicilios->exchangeArray($form->getData());
                 $this->getdomiciliosTable()->savedomicilios($domicilios);

                 return $this->redirect()->toRoute('domicilios');
             }
         }
         return array('form' => $form);     
     }

     public function editAction()
     {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('domicilios', array(
                'action' => 'add'
            ));
        }
        $domicilios = $this->getdomiciliosTable()->getdomicilios($id);

        $form  = new domiciliosForm($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        $form->bind($domicilios);
        $form->get('submit')->setAttribute('value', 'Guardar');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($domicilios->getInputFilter());
			 $post = array_merge_recursive(
	            $request->getPost()->toArray(),
	            $request->getFiles()->toArray()
	        );
             $form->setData($post);
            if ($form->isValid()) {
                $this->getdomiciliosTable()->savedomicilios($form->getData());

                return $this->redirect()->toRoute('domicilios');
            }
        }

 $sm = $this->getServiceLocator();

        return array(
            'id' => $id,
            'form' => $form,
 	    'sm' => $sm
        );
     }

     public function deleteAction()
     {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('domicilios', array(
                'action' => 'add'
            ));
        }
        $this->getdomiciliosTable()->deletedomicilios($id);
		return $this->redirect()->toRoute('domicilios');
     }
     
     public function getdomiciliosTable()
     {
         if (!$this->domiciliosTable) {
             $sm = $this->getServiceLocator();
             $this->domiciliosTable = $sm->get('domicilios\Model\domiciliosTable');
         }
         return $this->domiciliosTable;
     }
 }
 
?>
