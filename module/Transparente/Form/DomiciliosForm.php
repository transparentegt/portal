<?php
namespace Transparente\Form;
use Zend\Form\Form;

class DomiciliosForm extends Form
{

    private $adapter;

    public function __construct ($dbAdapter, $name = null)
    {
        parent::__construct($name);
        $this->adapter = $dbAdapter;
        $this->add(
                array(
                        'name' => 'id',
                        'type' => 'Hidden'
                ));
        $this->add(
                array(
                        'name' => 'updated',
                        'type' => 'Hidden'
                ));
        $this->add(
                array(
                        'name' => 'id_municipio',
                        'type' => 'Select',
                        'options' => array(
                                'label' => 'Departamento',
                                'value_options' => $this->getMunicipios()
                        )
                )
                );
        $this->add(
                array(
                        'name' => 'dirección',
                        'type' => 'Text',
                        'options' => array(
                                'label' => 'Dirección'
                        )
                ));
        $this->add(
                array(
                        'name' => 'telefonos',
                        'type' => 'Text',
                        'options' => array(
                                'label' => 'Teléfonos'
                        )
                ));
        $this->add(
                array(
                        'name' => 'fax',
                        'type' => 'Text',
                        'options' => array(
                                'label' => 'Fax'
                        )
                ));
        $this->add(
                array(
                        'name' => 'submit',
                        'type' => 'Submit',
                        'attributes' => array(
                                'value' => 'Guardar',
                                'id' => 'submitbutton'
                        )
                ));
    }

    private function getMunicipios ()
    {
        $dbAdapter = $this->adapter;
        $sql = 'SELECT id,nombre  FROM geo_municipios where 1 ORDER BY nombre ASC';
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();

        $selectData = array();

        foreach ($result as $res) {
            $selectData[$res['id']] = $res['nombre'];
        }
        return $selectData;
    }
}