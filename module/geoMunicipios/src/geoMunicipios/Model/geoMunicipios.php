<?php

namespace geoMunicipios\Model;

use Zend\InputFilter\Factory as InputFactory;     
use Zend\InputFilter\InputFilter;              
use Zend\InputFilter\InputFilterAwareInterface;   
use Zend\InputFilter\InputFilterInterface; 

 class geoMunicipios
 {
     public $id;
     public $nombre;
	 public $id_geo_departamento;
             
     protected $inputFilter;

     public function exchangeArray($data)
     {
         $this->id     = (!empty($data['id'])) ? $data['id'] : null;
         $this->nombre = (!empty($data['nombre'])) ? $data['nombre'] : null;
		 $this->id_geo_departamento = (!empty($data['id_geo_departamento'])) ? $data['id_geo_departamento'] : null;
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
                 'name'     => 'nombre',
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
                 'name'     => 'id_geo_departamento',
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