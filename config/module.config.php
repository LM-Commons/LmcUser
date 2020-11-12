<?php

return [
    'view_manager' => [
        'template_path_stack' => [
            'lmcuser' => __DIR__ . '/../view',
        ],
    ],

    'router' => [
        'routes' => [
            'lmcuser' => [
                'type' => 'Literal',
                'priority' => 1000,
                'options' => [
                    'route' => '/user',
                    'defaults' => [
                        'controller' => 'lmcuser',
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'login' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/login',
                            'defaults' => [
                                'controller' => 'lmcuser',
                                'action'     => 'login',
                            ],
                        ],
                    ],
                    'authenticate' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/authenticate',
                            'defaults' => [
                                'controller' => 'lmcuser',
                                'action'     => 'authenticate',
                            ],
                        ],
                    ],
                    'logout' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/logout',
                            'defaults' => [
                                'controller' => 'lmcuser',
                                'action'     => 'logout',
                            ],
                        ],
                    ],
                    'register' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/register',
                            'defaults' => [
                                'controller' => 'lmcuser',
                                'action'     => 'register',
                            ],
                        ],
                    ],
                    'changepassword' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/change-password',
                            'defaults' => [
                                'controller' => 'lmcuser',
                                'action'     => 'changepassword',
                            ],
                        ],
                    ],
                    'changeemail' => [
                        'type' => 'Literal',
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
