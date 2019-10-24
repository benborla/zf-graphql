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
    public $created_at;

    /** @var \Application\Model\Post[] */
    private $posts;

    /** @var array */
    protected $relations = [
        'posts'
    ];

    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        $this->created_at = date('Y-m-d H:i:s');
    }

    /**
     * @param \Application\Model\Post[] $post
     *
     * @return \Application\Model\User
     */
    public function setPosts($post)
    {
        $this->posts = $post;

        return $this;
    }

    /**
     * @return \Applicaiton\Model\Post[]
     */
    public function getPosts(): Post
    {
        return $this->posts;
    }
}
