<?php

namespace Application\Model\Table;

use Application\GraphQL\Type\QueryType;
use Application\GraphQL\Type\UserType;
use Application\GraphQL\Types;
use Application\Model\Table\AbstractTable;
use Application\Model\User;
use GraphQL\Type\Definition\Type;

class UserTable extends AbstractTable
{
    /**
     * @return array
     */
    public function usersQuery(): array
    {
        return [
            'type' => Type::listOf(Types::user()),
            'args' => [
                'name' => Type::string(),
                'position' => Type::string(),
            ],
            'resolve' => function ($root, $args) {
                return $this->fetchAll(false, $args);
            }
        ];
    }

    /**
     * @return array
     */
    public function userQuery(): array
    {
        return [
            'type' => Types::user(),
            'args' => [
                'id' => Type::int(),
            ],
            'resolve' => function ($root, $args) {
                return $this->get($args['id']);
            }
        ];
    }

    /**
     * @return array
     */
    public function createMutation(): array
    {
        return [
            'type' => Types::user(),
            'args' => [
                'name' => Type::nonNull(Type::string()),
                'position' => Type::nonNull(Type::string())
            ],
            'resolve' => function ($create, $args) {
                $user = new User();
                $user->name = $args['name'];
                $user->position = $args['position'];

                return $this->save($user);
            }
        ];
    }

    /**
     * @return array
     */
    public function updateMutation(): array
    {
        return [
            'type' => Types::user(),
            'args' => [
                'id' => Type::nonNull(Type::int()),
                'name' => Type::string(),
                'position' => Type::string()
            ],
            'resolve' => function ($update, $args) {
                $id = $args['id'];
                /** @var \Application\Model\User $user */
                $user = $this->get($id);
                $user->name = $args['name'] ?? $user->name;
                $user->position = $args['position'] ?? $user->position;

                return $this->save($user, $id);
            }

        ];
    }

    /**
     * @return array
     */
    public function deleteMutation(): array
    {
        return [
            'type' => Type::boolean(),
            'args' => [
                'id' => Type::nonNull(Type::int())
            ],
            'resolve' => function ($delete, $args) {
                $id = $args['id'] ?? null;
                if ($this->delete($id)) {
                    return true;
                }

                return false;
            }
        ];
    }

}
