<?php

declare(strict_types=1);

namespace Application\Model\Factory;

use Application\Model\Table\PostTable;
use Application\Model\Post;
use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ObjectPropertyHydrator;

class PostTableFactory
{
    private const TABLE = 'posts';

    public function __invoke(ContainerInterface $container)
    {
        $hydrator = new HydratingResultSet(
            new ObjectPropertyHydrator(),
            new Post()
        );

        $tableGateway = new TableGateway(
            self::TABLE,
            $container->get(AdapterInterface::class),
            null,
            $hydrator
        );

        return new PostTable($tableGateway);
    }
}
