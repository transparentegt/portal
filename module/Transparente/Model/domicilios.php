<?php

namespace Transparente\Model;

use Zend\InputFilter\Factory as InputFactory;     
use Zend\InputFilter\InputFilter;              
use Zend\InputFilter\InputFilterAwareInterface;   
use Zend\InputFilter\InputFilterInterface; 

 class domicilios
 {
     public $id;
	 public $id_municipio;
     public $direccion;
	 public $telefonos;
	 public $fax;
	 public $updated;
             
     protected $inputFilter;

     public function exchangeArray($data)
     {
         $this->id     = (!empty($data['id'])) ? $data['id'] : null;
		 $this->id_municipio = (!empty($data['id_municipio'])) ? $data['id_municipio'] : null;
		 $this->direccion = (!empty($data['direccion'])) ? $data['direccion'] : null;
		 $this->telefonos = (!empty($data['telefonos'])) ? $data['telefonos'] : null;
		 $this->fax = (!empty($data['fax'])) ? $data['fax'] : null;
		 $this->updated = date('Y-m-d h:i:s');
     }
     
     public function getArrayCopy()
     {
         return get_object_vars($this);
     }     
     
     public function setInputFilter(InputFilterInterface $inputFilter)
     {
         throw new \Exception("Not used");
     }

     public function getInputFilter()
     {
         if (!$this->inputFilter) {
             $inputFilter = new InputFilter();

             $inputFilter->add(array(
                 'name'     => 'id',
                 'required' => true,
                 'filters'  => array(
                     array('name' => 'Int'),
                 ),
             ));

             $inputFilter->add(array(
                 'name'     => 'telefonos',
                 'required' => true,
                 'filters'  => array(
                     array('name' => 'StripTags'),
                     array('name' => 'StringTrim'),
                 ),
                 'validators' => array(
                     array(
                         'name'    => 'StringLength',
                         'options' => array(
                             'encoding' => 'UTF-8',
                             'min'      => 1,
                             'max'      => 255,
                         ),
                     ),
                 ),
             ));

             $inputFilter->add(array(
                 'name'     => 'direccion',
                 'required' => true,
                 'filters'  => array(
                     array('name' => 'StripTags'),
                     array('name' => 'StringTrim'),
                 ),
                 'validators' => array(
                     array(
                         'name'    => 'StringLength',
                         'options' => array(
                             'encoding' => 'UTF-8',
                             'min'      => 1,
                             'max'      => 255,
                         ),
                     ),
                 ),
             ));
			 
             $inputFilter->add(array(
                 'name'     => 'fax',
                 'required' => true,
                 'filters'  => array(
                     array('name' => 'StripTags'),
                     array('name' => 'StringTrim'),
                 ),
                 'validators' => array(
                     array(
                         'name'    => 'StringLength',
                         'options' => array(
                             'encoding' => 'UTF-8',
                             'min'      => 1,
                             'max'      => 255,
                         ),
                     ),
                 ),
             ));

             $inputFilter->add(array(
                 'name'     => 'id_municipio',
                 'required' => true,
                 'filters'  => array(
                     array('name' => 'Int'),
                 ),
             ));
			 

             $this->inputFilter = $inputFilter;
         }

         return $this->inputFilter;
     }
}
 
 ?>