<?php

declare(strict_types=1);

namespace Application\GraphQL\Type;

use Application\Model\User;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

/**
 * @class UserType
 * https://github.com/webonyx/graphql-php/blob/master/examples/01-blog/Blog/Type/UserType.php#L54
 */
class UserType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Users',
            'description' => 'User collection',
            'fields' => function () {
                return [
                    'id' => Type::int(),
                    'name' => [
                        'type' => Type::string()
                    ],
                    'position' => [
                        'type' => Type::string()
                    ],
                    'created_at' => [
                        'type' => Type::string()
                    ],
                    'posts' => Type::string()
                ];
            },
            'resolveField' => function($user, $args, $context, ResolveInfo $info) {
                $method = 'resolve' . ucfirst($info->fieldName);
                if (method_exists($this, $method)) {
                    return $this->{$method}($user, $args, $context, $info);
                } else {
                    return $user->{$info->fieldName};
                }
            }
        ];

        parent::__construct($config);
    }
}
