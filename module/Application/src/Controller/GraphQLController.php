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
        $q = $this->getDataRequest()['query'];
        $query = $q['query'] ?? null;
        $variables = $q['variables'] ?? [];

        $data = $this->user->fetchAll()->toArray();

        $schema = new Schema([
            'query' => new UserType()
        ]);

        $result = GraphQL::executeQuery(
            $schema,
            $query,
            $data,
            null,
            $variables
        );

        return new JsonModel($result->toArray(true));
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
