<?php

namespace Application\Model;

abstract class AbstractModel
{
    /** @var array */
    protected $relations = [];

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
        $props = get_object_vars($this);

        foreach ($props as $prop => $value) {
            if (in_array($prop, $this->getRelations())) {
                unset($props[$prop]);
            }
        }

        unset($props['relations']);

        return $props;
    }

    /**
     * @return array
     */
    public function getRelations(): array
    {
        return $this->relations;
    }
}
