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
     * Retorna las propiedades del modelo para ser seteadas
     *
     * @return multitype:
     *
     * @todo retorna también las privadas?
     */
    public function asArray()
    {
        return get_object_vars($this);
    }

    /**
     * Aplica los valores del arreglo de datos a la entidad
     *
     * Requerimiento del ResultSet. Retorna los nuevos valores actuales de la entidad
     *
     * @param array $data
     *
     * @return array
     */
    public function exchangeArray($data)
    {
        $props = $this->asArray();
        foreach ($props as $key => $value) {
            if (isset($data[$key])) {
                $setter = 'set'.ucfirst($key);
                if (method_exists($this, $setter)) {
                    $this->$setter($data[$key]);
                } elseif (!is_array($data[$key])) {
                    $this->$key = $data[$key];
                }
            }
        }
        return $this->asArray();
    }

}