<?php

namespace Application\Model\Table;

use Application\GraphQL\Type\QueryType;
use Application\GraphQL\Type\UserType;
use Application\GraphQL\Types;
use Application\Model\Post;
use Application\Model\Table\AbstractTable;
use Application\Model\User;
use GraphQL\Type\Definition\Type;
use Expression;

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
                'postLimit' => Type::int()
            ],
            'resolve' => function ($root, $args) {
                return $this->get($args['id'], $args['postLimit'] ?? 0);
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

    // public function fetchAll(array $criteria)
    // {
    //     // modify this one so we would be able to fetch posts relationships
    //     return parent::fetchAll(false, $criteria);
    // }

    /**
     * @param int $id
     * @param null|int $postLimit
     *
     * @return \Application\Model\User
     */
    public function get(int $id, int $postLimit = 0)
    {
        return $this->getUserAndPosts($id, [], $postLimit)->getObjectPrototype();
    }

    /**
     * @param int $id
     * @param array $criteria
     *
     * @return \Zend\Db\Sql\Select
     */
    public function getUserAndPosts(int $id, array $criteria = [], int $limit = 0)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns([$select::SQL_STAR], true)
               ->join('posts', 'posts.user_id = users.id', [
                   // re-alias to identify which is is which
                   'posts.id' => 'id',
                   'posts.user' => 'user_id',
                   'posts.title' => 'title',
                   'posts.content' => 'content',
                   'posts.created_at' => 'created_at',
                ])
                ->where->equalTo('users.id', $id);

        if (is_array($criteria) && count($criteria)) {
            foreach ($criteria as $field => $value) {
                $select->where->like($field, "%$value%");
            }
        }

        if ($limit) {
            $select->limit($limit);
        }

        // dd($this->tableGateway->getSql()->getSqlstringForSqlObject($select));

        return $this->hydrateRelationPrototypeCollection(
            $this->tableGateway->selectWith($select),
            new User(),
            new Post()
        );
    }
}
