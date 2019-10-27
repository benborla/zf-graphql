<?php
namespace Application\Model;

// use Application\Model\AbstractModel;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="\Application\Model\Repository\UserRepository")
 * @ORM\Table(name="users")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id")
     */
    public $id;

    /**
     * @ORM\Column(name="name")
     */
    public $name;

    /**
     * @ORM\Column(name="position")
     */
    public $position;

    /**
     * @ORM\Column(name="created_at")
     */
    public $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="\Application\Model\Post", mappedBy="user")
     * @ORM\JoinColumn(name="id", referencedColumnName="user_id")
     */
    public $posts;

    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        $this->posts = new ArrayCollection();
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
     *
     * @return \Application\Model\User
     */
    public function setId(int $id): User
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return \Application\Model\User
     */
    public function setName(string $name): User
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getPosition(): string
    {
        return $this->position;
    }

    /**
     * @param string $position
     *
     * @return \Application\Model\User
     */
    public function setPosition(string $position): User
    {
        $this->position = $position;

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
     * @param mixed $createdAt
     *
     * @return \Application\Model\User
     */
    public function setCreatedAt($createdAt): User
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \Applicaiton\Model\Post[]
     */
    public function getPosts()
    {
        return $this->posts->getValues();
    }
} // End class User
