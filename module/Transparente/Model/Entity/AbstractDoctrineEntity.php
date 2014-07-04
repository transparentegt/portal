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
     * Requerimiento del ResultSet
     *
     * @param array $data
     *
     * @todo usar setters en vez de mandar a llamar a la propiedad directamente
     */
    public function exchangeArray($data)
    {
        $props = $this->asArray();
        foreach ($props as $key => $value) {
            if (isset($data[$key]) && !is_array($data[$key])) {
                $this->$key = $data[$key];
            }
        }
        return $this->asArray();
    }

}