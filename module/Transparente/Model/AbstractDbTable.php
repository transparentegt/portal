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

    /**
     *
     * @param AbstractDbModel $element
     *
     * @return AbstractDbModel
     */
    public function save(AbstractDbModel $element)
    {
        $data   = $element->asArray();
        if ((empty($data['id'])) || (!$this->getById($data['id']))) {
            $stored = $this->tableGateway->insert($data);
            if (!empty($data['id'])) {
                $model = $this->getById($data['id']);
            } else  {
                $model = $this->getById($this->tableGateway->lastInsertValue);
            }
        } else {
            $stored = $this->tableGateway->update($data, array('id' => $data['id']));
            $model  = $this->getById($data['id']);
        }
        return $model;
    }

    public function delete($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}