<?php
namespace Transparente\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Domicilios
{

    public $id;

    public $id_municipio;

    public $dirección;

    public $teléfonos;

    public $fax;

    public $updated;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id           = (! empty($data['id'])) ? $data['id'] : null;
        $this->id_municipio = (! empty($data['id_municipio'])) ? $data['id_municipio'] : null;
        $this->dirección    = (! empty($data['dirección'])) ? $data['dirección'] : null;
        $this->teléfonos    = (! empty($data['teléfonos'])) ? $data['teléfonos'] : null;
        $this->fax          = (! empty($data['fax'])) ? $data['fax'] : null;
        $this->updated      = (! empty($data['updated'])) ? $data['updated'] : null;
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