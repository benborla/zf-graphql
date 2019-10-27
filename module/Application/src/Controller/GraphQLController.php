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
use Psr\Container\ContainerInterface;
use ZF\Doctrine\GraphQL\Type\Loader as TypeLoader;
use ZF\Doctrine\GraphQL\Filter\Loader as FilterLoader;
use ZF\Doctrine\GraphQL\Resolve\Loader as ResolveLoader;
use ZF\Doctrine\GraphQL\Context;
use Doctrine\ORM\EntityManager;

/**
 * @class GraphQLController
 */
class GraphQLController extends AbstractActionController
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @param \Psr\Container\ContainerInterface $container
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return \Zend\View\Model\JsonModel
     */
    public function queryAction()
    {
        $query = $this->getDataRequest()['query'];
        $variables = $this->getDataRequest()['variables'];

        try {
            $result = GraphQL::executeQuery(
                $this->getSchema(),
                $query,
                null,
                $this->getContext(),
                $variables
            );

            $output = $result->toArray();

        } catch(\Exception $e) {
            dd($e);
            $output = [
                'errors' => [
                    ['exception' => $e->getMessage()]
                ]
            ];
        }

        return new JsonModel($output);
    }

    /**
     * @return \GraphQL\Type\Schema
     */
    protected function getSchema()
    {
        return new Schema([
            'query' => new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'users' => $this->em->getRepository(User::class)->getUsersType(),
                    'user' => $this->em->getRepository(User::class)->getUserType()
                ]
            ]),
            'mutation' => new ObjectType([
                'name' => 'Mutation',
                'fields' => [
                    'createUser' => $this->em->getRepository(User::class)->createUserMutation($this->em),
                    'updateUser' => $this->em->getRepository(User::class)->updateUserMutation($this->em),
                    'deleteUser' => $this->em->getRepository(User::class)->deleteUserMutation($this->em)

                ]
            ])
        ]);
    }

    /**
     * @return \ZF\Doctrine\GraphQL\Context
     */
    protected function getContext(): Context
    {
        return (new Context)
            ->setLimit(1000)
            ->setHydratorSection('default')
            ->setUseHydratorCache(true);
    }

    /**
     * @return \ZF\Doctrine\GraphQL\Type\Loader
     */
    protected function getTypeLoader()
    {
        return $this->container->get(TypeLoader::class);
    }

    /**
     * @return \ZF\Doctrine\GraphQL\Filter\Loader
     */
    protected function getFilterLoader()
    {
        return $this->container->get(FilterLoader::class);
    }

    /**
     * @return \ZF\Doctrine\GraphQL\Resolve\Loader
     */
    protected function getResolveLoader()
    {
        return $this->container->get(ResolveLoader::class);
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
