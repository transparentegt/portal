<?php

 namespace geoMunicipios\Controller;

 use Zend\Mvc\Controller\AbstractActionController;
 use Zend\View\Model\ViewModel;
 use geoMunicipios\Model\geoMunicipios;
 use geoMunicipios\Form\geoMunicipiosForm;

 class geoMunicipiosController extends AbstractActionController
 {
     protected $geoMunicipiosTable;	 
	 
    public function indexAction()
    {
	
        $paginator = $this->getgeoMunicipiosTable()->fetchAll(true);
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array(
            'paginator' => $paginator,
            'db' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter')
        ));
    }

    public function addAction()
     {		 
         $form = new geoMunicipiosForm($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
         $form->get('submit')->setValue('Agregar');

         $request = $this->getRequest();
         if ($request->isPost()) {
             $geoMunicipios = new geoMunicipios();
             $form->setInputFilter($geoMunicipios->getInputFilter());
			 $post = array_merge_recursive($request->getPost()->toArray(),
	            $request->getFiles()->toArray()
	        );
             $form->setData($post);
			 
             if ($form->isValid()) {
                 $geoMunicipios->exchangeArray($form->getData());
                 $this->getgeoMunicipiosTable()->savegeoMunicipios($geoMunicipios);

                 return $this->redirect()->toRoute('geoMunicipios');
             }
         }
         return array('form' => $form);     
     }

     public function editAction()
     {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('geoMunicipios', array(
                'action' => 'add'
            ));
        }
        $geoMunicipios = $this->getgeoMunicipiosTable()->getgeoMunicipios($id);

        $form  = new geoMunicipiosForm($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        $form->bind($geoMunicipios);
        $form->get('submit')->setAttribute('value', 'Guardar');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($geoMunicipios->getInputFilter());
			 $post = array_merge_recursive(
	            $request->getPost()->toArray(),
	            $request->getFiles()->toArray()
	        );
             $form->setData($post);
            if ($form->isValid()) {
                $this->getgeoMunicipiosTable()->savegeoMunicipios($form->getData());

                return $this->redirect()->toRoute('geoMunicipios');
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
            return $this->redirect()->toRoute('geoMunicipios', array(
                'action' => 'add'
            ));
        }
        $this->getgeoMunicipiosTable()->deletegeoMunicipios($id);
		return $this->redirect()->toRoute('geoMunicipios');
     }
     
     public function getgeoMunicipiosTable()
     {
         if (!$this->geoMunicipiosTable) {
             $sm = $this->getServiceLocator();
             $this->geoMunicipiosTable = $sm->get('geoMunicipios\Model\geoMunicipiosTable');
         }
         return $this->geoMunicipiosTable;
     }
 }
 
?>
