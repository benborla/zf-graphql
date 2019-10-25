<?php

namespace Application\Model;

use Application\Model\AbstractModel;
use Application\Model\User;

class Post extends AbstractModel
{
    /** @var int */
    public $id;

    /** @var \Application\Model\User */
    public $user;

    /** @var string */
    public $title;

    /** @var string */
    public $content;

    /** @var \DateTime */
    public $created_at;

    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        $this->created_at = date('Y-m-d H:i:s');
    }

    /**
     * @param \Application\Model\User $user
     *
     * @return \Application\Model\Post
     */
    public function setUser(User $user): Post
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->created_at;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return \Application\Model\Post
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

}
