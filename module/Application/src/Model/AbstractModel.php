<?php

namespace Application\Model;

abstract class AbstractModel
{
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
     * @return array
     */
    protected function toArray(): array
    {
        return get_object_vars($this);
    }
}
