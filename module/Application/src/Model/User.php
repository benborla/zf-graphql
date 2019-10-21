<?php

namespace App\Model;
// https://github.com/geerteltink/zf3-album-tutorial/blob/master/module/Album/src/Model/Album.php
class User
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $position;

    /**
     * @var \DateTime
     */
    public $createdAt;

    /**
     * @{inheritDoc}
     */
    public function __construct()
    {
        $this->createdAt = date('Y-m-d H:i:s');
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

}
