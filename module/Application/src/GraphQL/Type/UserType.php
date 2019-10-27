<?php

declare(strict_types=1);

namespace Application\GraphQL\Type;

use Application\Model\User;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Application\GraphQL\Types;

/**
 * @class UserType
 */
class UserType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'User',
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
                    'createdAt' => [
                        'type' => Type::string()
                    ],
                    'posts' => Type::listOf(Types::post())
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
