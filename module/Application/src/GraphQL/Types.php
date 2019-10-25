<?php

declare(strict_types=1);

namespace Application\GraphQL;

use Application\GraphQL\Type\UserType;
use Application\GraphQL\Type\PostType;

class Types
{
    /**
     * @var \Application\GraphQL\Type\UserType
     */
    private static $user;

    /**
     * @var \Application\GraphQL\Type\PostType
     */
    private static $post;


    /**
     * @return \Application\GraphQL\Type\UserType
     */
    public static function user(): UserType
    {
        return self::$user ?: (self::$user = new UserType());
    }

    /**
     * @return \Application\GraphQL\Type\PostType
     */
    public static function post(): PostType
    {
        return self::$post ?: (self::$post = new PostType());
    }


}
