<?php
namespace Application\Model\Repository;

use Application\GraphQL\Types;
use Application\Model\Post;
use Application\Model\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use GraphQL\Type\Definition\Type;

class UserRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getUsersType(): array
    {
        return [
            'type' => Type::listOf(Types::user()),
            'args' => [
                'name' => Type::string(),
                'position' => Type::string(),
                'createdAt' => Type::string()
            ],
            'resolve' => function ($root, $args) {
                return $this->findBy($args);
            }
        ];
    }

    /**
     * @return array
     */
    public function getUserType(): array
    {
        return [
            'type' => Types::user(),
            'args' => [
                'id' => Type::int(),
                'name' => Type::string(),
                'position' => Type::string(),
                'createdAt' => Type::string()
            ],
            'resolve' => function ($root, $args) {
                return $this->findOneBy($args);
            }
        ];
    }


    /**
     * @return array
     */
    public function createUserMutation(EntityManager $em): array
    {
        return [
            'type' => Types::user(),
            'description' => 'Create a new user',
            'args' => [
                'name' => Type::nonNull(Type::string()),
                'position' => Type::nonNull(Type::string())
            ],
            'resolve' => function ($create, $args) use ($em) {
                $user = new User();
                $user->setName($args['name']);
                $user->setPosition($args['position']);

                $em->persist($user);
                $em->flush();

                return $user;
            }
        ];
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @return array
     */
    public function updateUserMutation(EntityManager $em): array
    {
        return [
            'type' => Types::user(),
            'description' => 'Updates the user information',
            'args' => [
                'id' => Type::nonNull(Type::int()),
                'name' => Type::string(),
                'position' => Type::string()
            ],
            'resolve' => function ($create, $args) use ($em) {
                $user = $this->find($args['id']);

                $user->setName($args['name'] ?? $user->getName());
                $user->setPosition($args['position'] ?? $user->getPosition());

                $em->merge($user);
                $em->flush();

                return $user;
            }
        ];
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @return array
     */
    public function deleteUserMutation(EntityManager $em): array
    {
        return [
            'type' => Type::boolean(),
            'description' => 'Delete a user instance',
            'args' => [
                'id' => Type::nonNull(Type::int()),
            ],
            'resolve' => function ($create, $args) use ($em) {
                $success = false;

                try {
                    $user = $this->find($args['id']);
                } catch(\Exception $e) {
                    $success = false;
                }

                $success = $em->remove($user) ?? true;
                $em->flush();

                return $success;
            }
        ];
    }
}

