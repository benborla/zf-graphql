<?php

namespace Application\Model;

abstract class AbstractModel
{
    /** @var array */
    protected $relations = [
        'relations'
    ];

    /** @var int */
    public $id;

    /**
     * @param int|null $id
     *
     * @return \App\Model\AbstractModel
     */
    public function setId(?int $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param array $data
     */
    public function exchangeArray(array $data)
    {
        $props = $this->getArrayCopy();

        foreach ($props as $prop => $value) {
            $this->$prop = (!empty($data[$prop])) ? $data[$prop] : null; 
        }
    }

    /**
     * @return array
     */
    public function getArrayCopy(): array
    {
        // @Todo find a way to remove the property that is in
        // relations
        d($this->getRelations());
        d(get_object_vars($this));
        return array_intersect_assoc($this->getRelations(), get_object_vars($this));
    }

    /**
     * @return array
     */
    public function getRelations(): array
    {
        return $this->relations;
    }
}
