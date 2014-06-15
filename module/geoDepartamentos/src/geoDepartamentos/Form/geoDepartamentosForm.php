<?php

namespace geoDepartamentos\Form;

 use Zend\Form\Form;

 class geoDepartamentosForm extends Form
 {
     public function __construct($name = null)
     {
         parent::__construct($name);

         $this->add(array(
             'name' => 'id',
             'type' => 'Hidden',
         ));
         $this->add(array(
             'name' => 'nombre',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Nombre',
             ),
         ));
         $this->add(array(
             'name' => 'submit',
             'type' => 'Submit',
             'attributes' => array(
                 'value' => 'Guardar',
                 'id' => 'submitbutton',
             ),
         ));
    }
 }
 
 ?>