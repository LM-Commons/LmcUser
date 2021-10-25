<?php

namespace LmcUser;

use Laminas\Router\Http\Literal;

return [
    'view_manager' => [
        'template_path_stack' => [
            'lmcuser' => __DIR__ . '/../view',
        ],
    ],
    'router' => [
        'routes' => [
            'lmcuser' => [
                'type' => Literal::class,
                'priority' => 1000,
                'options' => [
                    'route' => '/user',
                    'defaults' => [
                        'controller' => 'lmcuser',
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'login' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/login',
                            'defaults' => [
                                'controller' => 'lmcuser',
                                'action' => 'login',
                            ],
                        ],
                    ],
                    'authenticate' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/authenticate',
                            'defaults' => [
                                'controller' => 'lmcuser',
                                'action' => 'authenticate',
                            ],
                        ],
                    ],
                    'otp' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/otp',
                            'defaults' => [
                                'controller' => 'lmcuser',
                                'action' => 'otp',
                            ],
                        ],
                    ],
                    'logout' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/logout',
                            'defaults' => [
                                'controller' => 'lmcuser',
                                'action' => 'logout',
                            ],
                        ],
                    ],
                    'register' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/register',
                            'defaults' => [
                                'controller' => 'lmcuser',
                                'action' => 'register',
                            ],
                        ],
                    ],
                    'changepassword' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/change-password',
                            'defaults' => [
                                'controller' => 'lmcuser',
                                'action' => 'changepassword',
                            ],
                        ],
                    ],
                    'changeemail' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/change-email',
                            'defaults' => [
                                'controller' => 'lmcuser',
                                'action' => 'changeemail',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
