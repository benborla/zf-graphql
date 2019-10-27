<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\Controller\IndexController;
use Application\Model\Table\UserTable;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;
use Application\Controller\GraphQLController;
use Application\Model\Factory\UserTableFactory;
use Application\Model\Factory\PostTableFactory;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Application\Model\User;
use Application\Model\Factory\UserFactory;

/**
 * @var \Zend\ServiceManager\ServiceManager $container
 */

return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'application' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/application[/:action]',
                    'defaults' => [
                        'controller' => IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'graphql' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/query',
                    'defaults' => [
                        'controller' => GraphQLController::class,
                        'action' => 'query'
                    ]
                ]
            ]
        ],
    ],
    'controllers' => [
        'factories' => [
            IndexController::class => function ($container) {
                return new IndexController(
                    $container->get('doctrine.entitymanager.orm_default')
                );
            },
            GraphQLController::class => function ($container) {
                return new GraphQLController(
                    $container->get('doctrine.entitymanager.orm_default')
                );
            }
        ],
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Model'],
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Model' => __NAMESPACE__ . '_driver'
                ]
            ]
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy'
        ]
    ],
    'service_manager' => [
        'factories' => [
        ]
    ]
];
