<?php

declare(strict_types=1);

namespace Application\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Application\GraphQL\Type\UserType;


class QueryType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Query',
            'fields' => [
                'users' => [
                    'type' => new UserType(),
                    'description' => 'Returns user by id',
                    'args' => [
                        'id' => Type::id()
                    ]
                ]
            ],
            'resolveField' => function ($rootValue, $args, $context, ResolveInfo $info) {
                return $this->{$info->fieldName}($rootValue, $args, $context, $info);
            }
        ];

        parent::__construct($config);
    }

}
