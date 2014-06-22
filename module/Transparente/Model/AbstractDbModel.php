<?php
namespace Transparente\Model;

/**
 * Clase abstracta para modelos tradicionales guardados en la por Transparente\Model\AbstractDbTable
 *
 * @todo probablemente deberíamos de cambiar a Doctrine o un ORM más robusto
 */
abstract class AbstractDbModel
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
     * Requerimiento del ResultSet
     *
     * @param array $data
     *
     * @see Zend\Db\ResultSet\ResultSet
     */
    public function exchangeArray($data)
    {
        $props = $this->asArray();
        foreach ($props as $key => $value) {
            $this->$key = (!empty($data[$key])) ? $data[$key] : null;
        }
        return $this->asArray();
    }

}