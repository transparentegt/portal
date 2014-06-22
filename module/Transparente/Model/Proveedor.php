<?php
namespace Transparente\Model;

/**
 *
 *
 * @todo Probar extends ArrayObject
 */
class Proveedor
{
    public $id;
    public $nombre;
    public $nit;
    public $status;
    public $tiene_acceso_sistema;
    public $id_domicilio_fiscal;
    public $id_domicilio_comercial;
    public $url;
    public $email;
    public $rep_legales_updated;
    public $tipo_organizacion;
    public $const_num_escritura;
    public $const_fecha;
    public $inscripcion_provisional;
    public $inscripcion_definitiva;
    public $inscripcion_sat;

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
    }

    public function asArray()
    {
        return get_object_vars($this);
    }

}
