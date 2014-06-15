<?php

namespace geoMunicipios\Form;

 use Zend\Form\Form;

 class geoMunicipiosForm extends Form
 {
 	 private $adapter;
		
     public function __construct($dbAdapter, $name = null)
     {
         parent::__construct($name);
		 $this->adapter = $dbAdapter;
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
		 	'name' => 'id_geo_departamento',
		 	'type' => 'Select',
             'options' => array(
                 'label' => 'Departamento',
                 'value_options' => $this->getDepartamentos()
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
	 
	private function getDepartamentos()
	{
        $dbAdapter = $this->adapter;
        $sql       = 'SELECT id,nombre  FROM geo_departamentos where 1 ORDER BY nombre ASC';
        $statement = $dbAdapter->query($sql);
        $result    = $statement->execute();

        $selectData = array();

        foreach ($result as $res) {
            $selectData[$res['id']] = $res['nombre'];
        }
        return $selectData;
	}
 }
 
 ?>