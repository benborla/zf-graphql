<?php

declare(strict_types=1);

namespace Application\Model\Factory;

use Application\Model\Table\UserTable;
use Application\Model\User;
use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ObjectPropertyHydrator;

class UserTableFactory
{
    private const TABLE = 'users';

    public function __invoke(ContainerInterface $container)
    {
        $hydrator = new HydratingResultSet(
            new ObjectPropertyHydrator(),
            new User()
        );

        $tableGateway = new TableGateway(
            self::TABLE,
            $container->get(AdapterInterface::class),
            null,
            $hydrator
        );

        return new UserTable($tableGateway);
    }
}
