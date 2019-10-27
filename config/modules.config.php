<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

/**
 * List of enabled modules for this application.
 *
 * This should be an array of module namespaces used in the application.
 */
return [
    'ZF\Doctrine\GraphQL',
    'Zend\Mvc\Console',
    'Zend\Cache',
    'Zend\Form',
    'Zend\InputFilter',
    'Zend\Filter',
    'Zend\Paginator',
    'Zend\Hydrator',
    'Zend\Serializer',
    'Zend\Db',
    'Zend\Router',
    'Zend\Validator',
    'DoctrineModule',
    'DoctrineORMModule',
    'ZF\ApiProblem',
    'ZF\Hal',
    'Phpro\DoctrineHydrationModule',
    'ZF\Versioning',
    'ZF\ContentNegotiation',
    'ZF\Rpc',
    'ZF\MvcAuth',
    'ZF\Rest',
    'ZF\ContentValidation',
    // 'ZF\Apigility',
    // 'ZF\Apigility\Doctrine\Admin',
    // 'ZF\Apigility\Doctrine\Server',
    'ZF\Doctrine\QueryBuilder',
    'ZF\Doctrine\Criteria',
    'Application',
];
