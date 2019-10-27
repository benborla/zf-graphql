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
use Application\Model\Hydrator\UserHydrator;

class UserFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new User();
    }
}
