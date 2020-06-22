<?php

return array(
    'view_manager' => array(
        'template_path_stack' => array(
            'lmcuser' => __DIR__ . '/../view',
        ),
    ),

    'router' => array(
        'routes' => array(
            'lmcuser' => array(
                'type' => 'Literal',
                'priority' => 1000,
                'options' => array(
                    'route' => '/user',
                    'defaults' => array(
                        'controller' => 'lmcuser',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'login' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/login',
                            'defaults' => array(
                                'controller' => 'lmcuser',
                                'action'     => 'login',
                            ),
                        ),
                    ),
                    'authenticate' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/authenticate',
                            'defaults' => array(
                                'controller' => 'lmcuser',
                                'action'     => 'authenticate',
                            ),
                        ),
                    ),
                    'logout' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/logout',
                            'defaults' => array(
                                'controller' => 'lmcuser',
                                'action'     => 'logout',
                            ),
                        ),
                    ),
                    'register' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/register',
                            'defaults' => array(
                                'controller' => 'lmcuser',
                                'action'     => 'register',
                            ),
                        ),
                    ),
                    'changepassword' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/change-password',
                            'defaults' => array(
                                'controller' => 'lmcuser',
                                'action'     => 'changepassword',
                            ),
                        ),
                    ),
                    'changeemail' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/change-email',
                            'defaults' => array(
                                'controller' => 'lmcuser',
                                'action' => 'changeemail',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);
