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

        $schema = new Schema([
            'query' => $this->getQueryConfig()
        ]);

        $result = GraphQL::executeQuery($schema, $query, null, null, $variableValues);
        $output = $result->toArray();

        return new JsonModel($output);
    }

    protected function getQueryConfig()
    {
        return new ObjectType([
            'name' => 'Query',
            'fields' => [
                'users' => [
                    'type' => new UserType(),
                    'args' => [
                        'id' => Type::int(),
                    ],
                    'resolve' => function ($root, $args) {
                        if ($id = $args['id']) {
                            return $this->user->get($id);
                        }

                        return $this->user->fetchAll()->toArray();
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
