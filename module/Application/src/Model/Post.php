<?php
namespace Application\Model;

use Application\Model\AbstractModel;
use Application\Model\User;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Post
 * @ORM\Entity
 * @ORM\Table(name="posts")
 *
 */
class Post
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
     * @ORM\ManyToOne(targetEntity="\Application\Model\User", inversedBy="posts")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    public $user;

    /**
     * @var string
     * @ORM\Column(name="title")
     */
    public $title;

    /**
     * @var string
     * @ORM\Column(name="content")
     */
    public $content;

    /**
     * @ORM\Column(name="created_at")
     */
    public $createdAt;

    /**
     * @var \Application\Model\Comment
     * @ORM\OneToMany(targetEntity="\Application\Model\Comment", mappedBy="post")
     * @ORM\JoinColumn(name="id", referencedColumnName="post_id")
     */
    public $comments;

    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        $this->createdAt = date('Y-m-d H:i:s');
        $this->comments = new ArrayCollection();
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
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Post
     */
    public function setTitle(string $title): Post
    {
        $this->title = $title;

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

    /**
     * @return \Applicaiton\Model\Comment[]
     */
    public function getComments()
    {
        return $this->comments->getValues();
    }
}
