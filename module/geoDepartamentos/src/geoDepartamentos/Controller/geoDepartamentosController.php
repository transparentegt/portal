<?php

 namespace geoDepartamentos\Controller;

 use Zend\Mvc\Controller\AbstractActionController;
 use Zend\View\Model\ViewModel;
 use geoDepartamentos\Model\geoDepartamentos;
 use geoDepartamentos\Form\geoDepartamentosForm;

 class geoDepartamentosController extends AbstractActionController
 {
     protected $geoDepartamentosTable;	 
	 
    public function indexAction()
    {
	
        // grab the paginator from the AlbumTable
        $paginator = $this->getgeoDepartamentosTable()->fetchAll(true);
        // set the current page to what has been passed in query string, or to 1 if none set
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        // set the number of items per page to 10
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array(
            'paginator' => $paginator,
        ));
    }

    public function addAction()
     {		 
         $form = new geoDepartamentosForm();
         $form->get('submit')->setValue('Agregar');

         $request = $this->getRequest();
         if ($request->isPost()) {
             $geoDepartamentos = new geoDepartamentos();
             $form->setInputFilter($geoDepartamentos->getInputFilter());
			 $post = array_merge_recursive($request->getPost()->toArray(),
	            $request->getFiles()->toArray()
	        );
             $form->setData($post);
			 
             if ($form->isValid()) {
                 $geoDepartamentos->exchangeArray($form->getData());
                 $this->getgeoDepartamentosTable()->savegeoDepartamentos($geoDepartamentos);

                 return $this->redirect()->toRoute('geoDepartamentos');
             }
         }
         return array('form' => $form);     
     }

     public function editAction()
     {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('geoDepartamentos', array(
                'action' => 'add'
            ));
        }
        $geoDepartamentos = $this->getgeoDepartamentosTable()->getgeoDepartamentos($id);

        $form  = new geoDepartamentosForm();
        $form->bind($geoDepartamentos);
        $form->get('submit')->setAttribute('value', 'Guardar');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($geoDepartamentos->getInputFilter());
			 $post = array_merge_recursive(
	            $request->getPost()->toArray(),
	            $request->getFiles()->toArray()
	        );
             $form->setData($post);
            if ($form->isValid()) {
                $this->getgeoDepartamentosTable()->savegeoDepartamentos($form->getData());

                return $this->redirect()->toRoute('geoDepartamentos');
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
            return $this->redirect()->toRoute('geoDepartamentos', array(
                'action' => 'add'
            ));
        }
        $this->getgeoDepartamentosTable()->deletegeoDepartamentos($id);
		return $this->redirect()->toRoute('geoDepartamentos');
     }
     
     public function getgeoDepartamentosTable()
     {
         if (!$this->geoDepartamentosTable) {
             $sm = $this->getServiceLocator();
             $this->geoDepartamentosTable = $sm->get('geoDepartamentos\Model\geoDepartamentosTable');
         }
         return $this->geoDepartamentosTable;
     }
 }
 
?>
