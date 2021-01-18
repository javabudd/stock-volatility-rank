<?php

use Doctrine\DBAL\Driver\PDO\MySQL\Driver;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use IVRank\Controller\CliController;
use IVRank\Controller\Factory\CliController as CliControllerFactory;
use IVRank\Controller\WebController;
use Laminas\Mvc\Console\Router\Simple;
use Laminas\Router\Http\Literal;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'doctrine'     => [
        'connection' => [
            'orm_default' => [
                'driverClass' => Driver::class,
                'params'      => [
                    'host'     => '127.0.0.1',
                    'port'     => '3306',
                    'user'     => 'ivrank',
                    'password' => 'poopboobs',
                    'dbname'   => 'ivrank',
                ],
            ],
        ],
        'driver'     => [
            'default_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [
                    BASE_PATH . '/src/Data',
                ],
            ],
            'orm_default'    => [
                'drivers' => [
                    'IVRank\\Data' => 'default_driver',
                ],
            ],
        ],
    ],
    'view_manager' => [
        'display_exceptions'     => true,
        'display_startup_errors' => true,
        'strategies'             => [
            'ViewJsonStrategy',
        ],
        'not_found_template'     => 'error/404',
        'exception_template'     => 'error/index',
        'template_map'           => [
            'error/404'   => BASE_PATH . '/view/error/404.phtml',
            'error/index' => BASE_PATH . '/view/error/index.phtml',
        ],
        'template_path_stack'    => [
            BASE_PATH . '/view',
        ],
    ],
    'controllers'  => [
        'factories'  => [
            CliController::class => CliControllerFactory::class,
            WebController::class => InvokableFactory::class
        ],
        'delegators' => []
    ],
    'router'       => [
        'routes' => [
            'index' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => WebController::class,
                        'action'     => 'index'
                    ]
                ]
            ]
        ]
    ],
    'console'      => [
        'router' => [
            'routes' => [
                'scrape' => [
                    'type'    => Simple::class,
                    'options' => [
                        'route'    => 'scrape',
                        'defaults' => [
                            'controller' => CliController::class,
                            'action'     => 'scrape',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
