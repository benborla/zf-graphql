<?php

namespace Application\Model;

use Application\Model\AbstractModel;

class User extends AbstractModel
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var string */
    public $position;

    /** @var \DateTime */
    public $createdAt = date('Y-m-d H:i:s');

    /**
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

}
