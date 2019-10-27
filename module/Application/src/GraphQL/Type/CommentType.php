<?php

declare(strict_types=1);

namespace Application\GraphQL\Type;

use Application\Model\Comment;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Application\GraphQL\Types;

/**
 * Class CommentType
 */
class CommentType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Comment',
            'description' => 'Comment instance type',
            'fields' => function () {
                return [
                    'id' => Type::int(),
                    'user' => [
                        'type' => Types::user()
                    ],
                    'content' => [
                        'type' => Type::string()
                    ],
                    'created_at' => [
                        'type' => Type::string()
                    ],
                    'user' => [
                        'type' => Types::user(),
                        'args' => [
                            'id' => Type::int()
                        ]
                    ]
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
