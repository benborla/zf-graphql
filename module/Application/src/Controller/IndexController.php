<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use GraphQL\GraphQL;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Application\Model\Table\UserTable;

/**
 * @class IndexController
 *
 * https://github.com/geerteltink/zf3-album-tutorial/blob/master/module/Album/Module.php
 */
class IndexController extends AbstractActionController
{
    /** @var \Application\Model\Table\UserTable */
    private $user;

    public function __construct(UserTable $user)
    {
        dd('here');
        $this->user = $user;
    }

    public function indexAction()
    {
        return new ViewModel();
    }

    public function debugAction()
    {
        dd('end');
    }

    public function queryAction()
    {
        $output = [];
        $schema = new Schema([
            'query' => $this->getQueryType(),
            // 'mutation' => $this->getMutationType()
        ]);

        $rawInput = file_get_contents('php://input');
        $input = json_decode($rawInput, true);
        $query = $input['query'];
        $variableValues = isset($input['variables']) ? $input['variables'] : null;

        try {
            $rootValue = ['prefix' => 'You said: '];
            $result = GraphQL::executeQuery($schema, $query, $rootValue, null, $variableValues);
            $output = $result->toArray();
        } catch (\Exception $e) {
            $output = [
                'errors' => [
                    [
                        'message' => $e->getMessage()
                    ]
                ]
            ];
        }

        return new JsonModel(['data' => $output]);
    }

    /**
     * Access via GraphQL Playground:
     * query {
     *    echo(message: "hello, world")
     * }
     */
    protected function getQueryType()
    {
        return new ObjectType([
            'name' => 'Query',
            'fields' => [
                'echo' => [
                    'type' => Type::string(),
                    'args' => [
                        'message' => Type::nonNull(Type::string())
                    ],
                    'resolve' => function ($rootValue, $args) {
                        return $rootValue['prefix'] . $args['message'];
                    }
                ]
            ],
        ]);

    }

    protected function getMutationType()
    {
        return new ObjectType([

            'name' => ''
        ]);
    }
} // End class IndexController
