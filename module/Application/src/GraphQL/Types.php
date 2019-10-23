<?php

declare(strict_types=1);

namespace Application\GraphQL;

use Application\GraphQL\Type\UserType;

class Types
{
    /**
     * @var \Application\GraphQL\Type\UserType
     */
    private static $user;

    /**
     * @return \Application\GraphQL\Type\UserType
     */
    public static function user(): UserType
    {
        return self::$user ?: (self::$user = new UserType());
    }

}
