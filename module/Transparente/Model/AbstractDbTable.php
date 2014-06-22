<?php
namespace Transparente\Model;

use Zend\Db\TableGateway\TableGateway;
/**
 * Clase abstracta para
 *
 * @todo probablemente deberíamos de cambiar a Doctrine o un ORM más robusto
 */
class AbstractDbTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $rs = $this->tableGateway->select();
        return $rs;
    }

    public function getById($id)
    {
        $id = (int) $id;
        $rs = $this->tableGateway->select(['id' => $id])->current();
        return $rs;
    }

    public function save(AbstractDbModel $element)
    {
        $data = $element->asArray();
        if (!$this->getById($data['id'])) {
            $return = $this->tableGateway->insert($data);
        } else {
            $return = $this->tableGateway->update($data, array('id' => $data['id']));
        }
        return $return;
    }

    public function delete($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}