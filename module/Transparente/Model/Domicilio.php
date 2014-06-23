<?php
namespace Transparente\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

class Domicilio extends AbstractDbModel
{

    public $id;
    public $id_municipio;
    public $direccion;
    public $teléfonos;
    public $fax;
    public $updated;

    protected $inputFilter;

    private function detectarMunicipio($data)
    {
        if (!empty($data['departamento']) && !empty($data['municipio'])) {
            $departamento = $data['departamento'];
            $departamento = strtolower($departamento);
            $departamento = ucfirst($departamento);
            $municipio    = $data['municipio'];
            $municipio    = strtolower($municipio);
            $municipio    = ucfirst($municipio);
        }
    }


    public function exchangeArray($data)
    {
        $data = $this->detectarMunicipio($data);
        echo '<pre><strong>DEBUG::</strong> '.__FILE__.' +'.__LINE__."\n"; var_dump($data); die();

    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name'     => 'id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int')
                )
            ));

            $inputFilter->add(array(
                'name' => 'teléfonos',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim')
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 255
                        )
                    )
                )
            ));

            $inputFilter->add(array(
                'name' => 'dirección',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'StripTags'
                    ),
                    array(
                        'name' => 'StringTrim'
                    )
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 255
                        )
                    )
                )
            ));

            $inputFilter->add(array(
                'name' => 'fax',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'StripTags'
                    ),
                    array(
                        'name' => 'StringTrim'
                    )
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 255
                        )
                    )
                )
            ));

            $inputFilter->add(array(
                'name' => 'id_municipio',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    )
                )
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}