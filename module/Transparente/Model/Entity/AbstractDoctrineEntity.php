<?php
namespace Transparente\Model\Entity;

/**
 * Clase abstracta para modelos tradicionales guardados en la por Transparente\Model\AbstractDbTable
 *
 * @todo probablemente deberíamos de cambiar a Doctrine o un ORM más robusto
 */
abstract class AbstractDoctrineEntity
{

    /**
     * Método ANTES de usar getArrayCopy para Apigility.
     *
     * Creo que podemos remplazar todas las llamadas por getArrayCopy()
     *
     * @return array
     * @deprecated
     */
    public function asArray()
    {
        return $this->getArrayCopy();
    }

    /**
     * Retorna las propiedades del modelo para ser seteadas
     *
     * Apigility pide este meodo para barrer las propiedades
     *
     * @return array
     *
     * @see ArrayIterator
     *
     * @todo retorna también las privadas?
     * @todo debería de implementar o extender alguna clase como ArrayObject
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * Aplica los valores del arreglo de datos a la entidad.
     *
     * Asigna usando setters de la entidad, si no hay setter definido, lo asigna a la propiedad
     * directamente para no tener que escribir todas las propiedades
     *
     * @param  array $data Llave y valor de los campos a asociar a la entidad
     * @return array       Retorna los nuevos valores que tiene la entidad
     */
    public function exchangeArray($data)
    {
        $props = $this->asArray();
        foreach ($props as $key => $value) {
            if (isset($data[$key])) {
                $setter = 'set'.str_replace(' ','',ucwords(str_replace('_',' ',$key)));;
                if (method_exists($this, $setter)) {
                    // try {
                        $this->$setter($data[$key]);
                    // } catch (\Exception $e) {
                    //    $this->$key = $data[$key];
                    // }
                } elseif (!is_array($data[$key])) {
                    $this->$key = $data[$key];
                }
            }
        }
        return $this->asArray();
    }

}