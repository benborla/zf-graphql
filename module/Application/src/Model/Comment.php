<?php
namespace Application\Model;

use Application\Model\User;
use Application\Model\Post;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Comment
 * @ORM\Entity
 * @ORM\Table(name="comments")
 *
 */
class Comment
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id")
     */
    public $id;

    /**
     * @var \Application\Model\User
     * @ORM\OneToOne(targetEntity="\Application\Model\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    public $user;

    /**
     * @var string
     * @ORM\Column(name="content")
     */
    public $content;

    /**
     * @var \Application\Model\User
     * @ORM\ManyToOne(targetEntity="\Application\Model\Post", inversedBy="comments")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     */
    public $post;

    /**
     * @ORM\Column(name="created_at")
     */
    public $createdAt;

    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        $this->createdAt = date('Y-m-d H:i:s');
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return User
     */
    public function setId(int $id): Post
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return Post
     */
    public function setContent(string $content): Post
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return \Application\Model\User
     */
    public function setCreatedAt($createdAt): Post
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }

    /**
     * @param Post $post
     * @return Comment
     */
    public function setPost(Post $post): Comment
    {
        $this->post = $post;

        return $this;
    }

    /**
     * @param \Application\Model\User $user
     *
     * @return \Application\Model\Post
     */
    public function setUser(User $user): Post
    {
        $this->user = $user->getId();

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
