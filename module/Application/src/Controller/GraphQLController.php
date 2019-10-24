<?php

declare(strict_types=1);

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Application\Model\Table\UserTable;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use GraphQL\Type\Definition\Type;
use Application\GraphQL\Type\UserType;
use Application\GraphQL\Type\QueryType;
use Application\GraphQL\Types;
use Application\Model\User;

/**
 * @class GraphQLController
 */
class GraphQLController extends AbstractActionController
{
    /** @var \Application\Model\Table\UserTable */
    private $user;

    /**
     * @param \Application\Model\Table\UserTable $user
     */
    public function __construct(UserTable $user)
    {
        $this->user = $user;
    }

    /**
     * @return JsonModel
     */
    public function queryAction()
    {
        $request  = $this->getDataRequest();
        $query = $request['query'];
        $variables = $request['variables'];
        // $data = $this->user->fetchAll()->toArray();

        // move this to the UserTable 
        // make it static so it can be called here
        $schema = new Schema([
            'query' => $this->getQueryConfig(),
            'mutation' => $this->getMutationConfig()
        ]);

        $result = GraphQL::executeQuery($schema, $query, null, null, $variables);
        $output = $result->toArray();

        return new JsonModel($output);
    }

    /**
     * @return \GraphQL\Type\Definition\ObjectType
     */
    protected function getQueryConfig(): ObjectType
    {
        return new ObjectType([
            'name' => 'Query',
            'fields' => [
                'users' => [
                    'type' => Type::listOf(Types::user()),
                    'args' => [
                        'name' => Type::string(),
                        'position' => Type::string(),
                    ],
                    'resolve' => function ($root, $args) {
                        return $this->user->fetchAll(false, $args);
                    }
                ],
                'user' => [
                    'type' => Types::user(),
                    'args' => [
                        'id' => Type::int(),
                    ],
                    'resolve' => function ($root, $args) {
                        return $this->user->get($args['id']);
                    }
                ]
            ]
        ]);
    }

    /**
     * @return ObjectType
     */
    protected function getMutationConfig(): ObjectType
    {
        $user = new User();

        return new ObjectType([
            'name' => 'Mutation',
            'fields' => [
                'createUser' => [
                    'type' => Types::user(),
                    'args' => [
                        'name' => Type::nonNull(Type::string()),
                        'position' => Type::nonNull(Type::string())
                    ],
                    'resolve' => function ($create, $args) use ($user) {
                        $user->name = $args['name'];
                        $user->position = $args['position'];

                        return $this->user->save($user);
                    }
                ],
                'updateUser' => [
                    'type' => Types::user(),
                    'args' => [
                        'id' => Type::nonNull(Type::int()),
                        'name' => Type::string(),
                        'position' => Type::string()
                    ],
                    'resolve' => function ($update, $args) {
                        $id = $args['id'];
                        $user = $this->user->get($id);
                        $user->name = $args['name'] ?? $user->name;
                        $user->position = $args['position'] ?? $user->position;

                        return $this->user->save($user, $id);
                    }

                ],
                'deleteUser' => [
                    'type' => Type::boolean(),
                    'args' => [
                        'id' => Type::nonNull(Type::int())
                    ],
                    'resolve' => function ($delete, $args) {
                        $id = $args['id'] ?? null;
                        if ($this->user->delete($id)) {
                            return true;
                        }

                        return false;
                    }
                ]
            ]
        ]);
    }

    /**
     * @return array
     */
    private function getDataRequest()
    {
        $rawInput = file_get_contents('php://input');
        $input = json_decode($rawInput, true);
        $query = $input['query'];
        $variableValues = isset($input['variables']) ? $input['variables'] : null;

        return [
            'query' => $query,
            'variables' => $variableValues
        ];
    }
}
