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
use Application\Model\User;
use Application\Model\Post;
use Doctrine\ORM\EntityManager;

/**
 * @class IndexController
 *
 * https://github.com/geerteltink/zf3-album-tutorial/blob/master/module/Album/Module.php
 */
class IndexController extends AbstractActionController
{
    /** @var \Doctrine\ORM\EntityManager */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function indexAction()
    {
        return new ViewModel();
    }

    public function addUserAction()
    {
        $user = new User();
        $user->name = "test etsjt";
        $user->position = "Admin";

        $user = $this->user->save($user);
        dd($user);
    }

    public function updateUserAction()
    {
        $id = 6;
        $user = $this->user->get($id);
        $user->name = "Bryan Brioso [Updated] 2";
        $user->position = "Senior Dev";


        dd($this->user->save($user, $id));
        dd("end");
    }

    public function postsAction()
    {
        $user = $this->em->getRepository(User::class)->find(2);
        $post = new Post();
        $post->setTitle("title");
        $post->setContent("hello");
        $post->setUser($user);



        $this->em->persist($post);
        $this->em->flush();

         die;

    }

    public function debugAction()
    {
        $test = $this->em->getRepository(User::class)->create([
            'name' => 'Marshall Law',
            'position' => 'test user'
        ]);
        dd($test);

        // dd($this->user::usersQuery());
        // $user = new User();
        // $user->exchangeArray([
        //     'id' => 1,
        //     'name' => 'test',
        //     'position' => 'hello'
        // ]);

        // dd($user->getArrayCopy());
        // $users = $this->user->fetchAll(false, ['name' => 'Bryan', 'position' => 'Developer']);
        // dd($users);
        // foreach ($this->user->fetchAll(false, ['name' => 'Bryan', 'position' => 'Developer']) as $user) {
        //     d($user->name);
        // }
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
}
