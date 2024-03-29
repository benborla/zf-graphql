<?php

namespace Application\Model\Table;

use \RuntimeException;
use App\Model\AbstractModel as Model;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\ResultSet\HydratingResultSet;

abstract class AbstractTable
{
    /** @var string */
    public $id = 'id';

    /** @var \Zend\Db\TableGateway\TableGatewayInterface */
    protected $tableGateway;

    /**
     * @param \Zend\Db\TableGateway\TableGatewayInterface $tableGateway
     */
    public function __construct(
        TableGatewayInterface $tableGateway,
        string $id = 'id'
    ) {
        $this->tableGateway = $tableGateway;
        $this->setIdKey($id);
    }

    /**
     * @param bool $paginated
     * @param null|array $criteria
     *
     * @return \Zend\Db\Sql\Select|\Zend\Paginator\Paginator
     */
    public function fetchAll($paginated = false, array $criteria = [])
    {
        if ($paginated) {
            return $this->fetchPaginatedResults();
        }

        $select = $this->tableGateway->getSql()->select();

        if (is_array($criteria) && count($criteria)) {
            foreach ($criteria as $field => $value) {
                $select->where->like($field, "%$value%");
            }
        }

        $collection = $this->tableGateway->selectWith($select);

        // dd($this->tableGateway->getSql()->getSqlstringForSqlObject($select));

        return $collection;
    }

    /**
     * @param int $id
     *
     * @return \Zend\Db\Sql\Select
     */
    public function get(int $id)
    {
        return $this->getBy($this->getIdKey(), $id);
    }

    /**
     * @param string $field
     * @param mixed $value
     *
     * @return \Zend\Db\Sql\Select
     */
    public function getBy(string $field, $value)
    {
        $rowset = $this->tableGateway->select([$field  => $value]);
        $row    = $rowset->current();
        if (!$row) {
            throw new RuntimeException(sprintf(
                'Could not find row with identifier %d',
                $value
            ));
        }

        return $row;
    }

    /**
     * @param \App\Model\AbstractModel $model
     *
     * @return mixed
     */
    public function save($model)
    {
        $data = $model->getArrayCopy();
        $id = $model->getId();

        if (null === $id || 0 === $id) {
            unset($data[$this->getIdKey()]);
            if ($this->tableGateway->insert($data)) {
                $id = $this->tableGateway->getLastInsertValue();
                return $this->get($id);
            }

            return;
        }

        if (!$this->get($id)) {
            throw new RuntimeException(sprintf(
                'Cannot update table  with identifier %d; does not exist',
                $id
            ));
        }

        if ($this->tableGateway->update($data, [$this->getIdKey() => $id])) {
            return $this->get($id);
        }
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function delete(int $id)
    {
        return $this->tableGateway->delete([$this->getIdKey() => $id]);
    }

    /**
     * @return int
     */
    public function getIdKey(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return \App\Model\Table\AbstractTable
     */
    public function setIdKey(string $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return \Zend\Paginator\Paginator
     */
    protected function fetchPaginatedResults()
    {
        $select = new Select($this->tableGateway->getTable());
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Model());
        $paginatorAdapter = new DbSelect(
            $select,
            $this->tableGateway->getAdapter(),
            $resultSetPrototype
        );
        $paginator = new Paginator($paginatorAdapter);

        return $paginator;
    }

    /**
     * @param string $table
     * @param array $haystack
     *
     * @return array
     */
    protected function extractResultSet(string $table, array $haystack): array
    {
        $extracted = [];

        if ($haystack) {
            foreach ($haystack as $key => $value) {
                if (strpos($key, $table) !== false) {
                    $key = trim(str_replace("$table.", null, $key));
                    $extracted[$key] = $value;
                }
            }
        }

        return $extracted;
    }

    /**
     * @param mixed $prefix
     * @param array $haystack
     *
     * @return array
     */
    public function removeDataWithPrefix($prefix, $haystack = []): array
    {
        $data = [];
        if ($haystack) {
            foreach ($haystack as $key => $value) {
                if (strpos($key, $prefix) !== false) {
                    unset($data[$key]);
                } else {
                    $data[$key] = $value;
                }
            }
        }
        return $data;
    }


    /**
     * @param \Zend\Db\ResultSet\HydratingResultSet $resultSet
     * @param object $objectPrototype
     * @param object $relationObjectPrototype
     *
     * @return \Zend\Db\ResultSet\HydratingResultSet
     */
    protected function hydrateRelationPrototypeCollection(
        HydratingResultSet $resultSet,
        $objectPrototype,
        $relationObjectPrototype
    ): HydratingResultSet {

        if (($object = $resultSet->getObjectPrototype()) instanceof $objectPrototype) {
            $targetClassName = explode('\\', strtolower(get_class($relationObjectPrototype)));
            $targetClassName = end($targetClassName) . 's';
            $data = [];

            $results = $resultSet->toArray();

            foreach ($results as $idx => $result) {
                $prototype = new $relationObjectPrototype();

                foreach ($this->extractResultSet($targetClassName, $result) as $property => $value) {
                    if (
                        !is_null($value) || 
                        (is_array($value) && !empty($value))
                    ) {
                        $property = explode('.', $property);
                        $property = end($property);
                        $prototype->$property = $value;
                    }
                }

                foreach ($this->removeDataWithPrefix($targetClassName, $result) as $prototypeProperty => $prototypeValue) {
                    if (!is_null($prototypeValue) || (is_array($prototypeValue) && !empty($prototypeValue))) {
                        $object->$prototypeProperty = $prototypeValue;
                    }
                }

                $data[] = $prototype;
            }

            $object->$targetClassName = $data;

            $resultSet->setObjectPrototype($object);

        }

        return $resultSet;
    }
}
