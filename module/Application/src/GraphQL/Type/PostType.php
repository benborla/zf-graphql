<?php

declare(strict_types=1);

namespace Application\GraphQL\Type;

use Application\Model\User;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Application\GraphQL\Types;

/**
 * @class PostType
 */
class PostType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Post',
            'description' => 'Post collection',
            'fields' => function () {
                return [
                    'id' => Type::int(),
                    'user' => [
                        'type' => Types::user()
                    ],
                    'title' => [
                        'type' => Type::string()
                    ],
                    'content' => [
                        'type' => Type::string()
                    ],
                    'created_at' => [
                        'type' => Type::string()
                    ],
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
