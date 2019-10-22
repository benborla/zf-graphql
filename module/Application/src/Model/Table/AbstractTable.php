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

abstract class AbstractTable
{
    /** @var string */
    public $id = 'id';

    /** @var \Zend\Db\TableGateway\TableGatewayInterface */
    private $tableGateway;

    /**
     * @param \Zend\Db\TableGateway\TableGatewayInterface $tableGateway
     */
    public function __construct(
        TableGatewayInterface $tableGateway,
        string $id = 'id'
    ) {
        $this->tableGateway = $tableGateway;
        $this->setId($id);
    }

    /**
     * @param bool $paginated
     *
     * @return \Zend\Db\Sql\Select|\Zend\Paginator\Paginator
     */
    public function fetchAll($paginated = false)
    {
        if ($paginated) {
            return $this->fetchPaginatedResults();
        }

        return $this->tableGateway->select();
    }

    /**
     * @param int $id
     *
     * @return \Zend\Db\Sql\Select
     */
    public function get(int $id)
    {
        return $this->getBy($this->getId(), $id);
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
    public function save(Model $model)
    {
        $data = $model->toArray();
        $id = $model->getId();

        if (null === $id || 0 === $id) {
            return $this->tableGateway->insert($data);
        }

        if (!$this->get($id)) {
            throw new RuntimeException(sprintf(
                'Cannot update table  with identifier %d; does not exist',
                $id
            ));
        }

        return $this->tableGateway->update($data, [$this->getId() => $id]);
    }

    /**
     * @param int $id
     *
     * @return \App\Model\Table\AbstractTable
     */
    public function delete(int $id)
    {
        $this->tableGateway->delete([$this->getId() => $id]);

        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return \App\Model\Table\AbstractTable
     */
    public function setId(string $id)
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
}