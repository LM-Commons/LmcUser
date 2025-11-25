<?php

declare(strict_types=1);

namespace LmcUser;

use Laminas\Router\Http\Literal;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies'       => $this->getDependencies(),
            'view_helpers'       => $this->getViewHelperConfig(),
            'view_manager'       => $this->getViewManagerConfig(),
            'router'             => $this->getRouterConfig(),
            'controllers'        => $this->getControllerConfig(),
            'controller_plugins' => $this->getControllerPluginConfig(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'aliases'   => [
                'lmcuser_register_form_hydrator' => 'lmcuser_user_hydrator',
            ],
            'factories' => [
                'lmcuser_redirect_callback'      => Factory\Controller\RedirectCallbackFactory::class,
                'lmcuser_module_options'         => Factory\Options\ModuleOptions::class,
                'lmcuser_login_form'             => Factory\Form\Login::class,
                'lmcuser_register_form'          => Factory\Form\Register::class,
                'lmcuser_change_password_form'   => Factory\Form\ChangePassword::class,
                'lmcuser_change_email_form'      => Factory\Form\ChangeEmail::class,
                Authentication\Adapter\Db::class => Factory\Authentication\Adapter\DbFactory::class,
                Authentication\Storage\Db::class => Factory\Authentication\Storage\DbFactory::class,
                'lmcuser_user_service'           => Service\UserServiceFactory::class,
            ],
        ];
    }

    public function getControllerPluginConfig(): array
    {
        return [
            'factories' => [
                'lmcUserAuthentication' => Factory\Controller\Plugin\LmcUserAuthentication::class,
            ],
        ];
    }

    public function getControllerConfig(): array
    {
        return [
            'factories' => [
                'lmcuser' => Factory\Controller\UserControllerFactory::class,
            ],
        ];
    }

    public function getViewHelperConfig(): array
    {
        return [
            'factories' => [
                'lmcUserDisplayName' => Factory\View\Helper\LmcUserDisplayName::class,
                'lmcUserIdentity'    => Factory\View\Helper\LmcUserIdentity::class,
                'lmcUserLoginWidget' => Factory\View\Helper\LmcUserLoginWidget::class,
            ],
        ];
    }

    public function getServiceConfig(): array
    {
        return $this->getDependencies();
    }

    public function getViewManagerConfig(): array
    {
        return [
            'template_path_stack' => [
                'lmcuser' => __DIR__ . '/../view',
            ],
        ];
    }

    public function getRouterConfig(): array
    {
        return [
            'routes' => [
                'lmcuser' => [
                    'type'          => Literal::class,
                    'priority'      => 1000,
                    'options'       => [
                        'route'    => '/user',
                        'defaults' => [
                            'controller' => 'lmcuser',
                            'action'     => 'index',
                        ],
                    ],
                    'may_terminate' => true,
                    'child_routes'  => [
                        'login'          => [
                            'type'    => Literal::class,
                            'options' => [
                                'route'    => '/login',
                                'defaults' => [
                                    'controller' => 'lmcuser',
                                    'action'     => 'login',
                                ],
                            ],
                        ],
                        'authenticate'   => [
                            'type'    => Literal::class,
                            'options' => [
                                'route'    => '/authenticate',
                                'defaults' => [
                                    'controller' => 'lmcuser',
                                    'action'     => 'authenticate',
                                ],
                            ],
                        ],
                        'logout'         => [
                            'type'    => Literal::class,
                            'options' => [
                                'route'    => '/logout',
                                'defaults' => [
                                    'controller' => 'lmcuser',
                                    'action'     => 'logout',
                                ],
                            ],
                        ],
                        'register'       => [
                            'type'    => Literal::class,
                            'options' => [
                                'route'    => '/register',
                                'defaults' => [
                                    'controller' => 'lmcuser',
                                    'action'     => 'register',
                                ],
                            ],
                        ],
                        'changepassword' => [
                            'type'    => Literal::class,
                            'options' => [
                                'route'    => '/change-password',
                                'defaults' => [
                                    'controller' => 'lmcuser',
                                    'action'     => 'changepassword',
                                ],
                            ],
                        ],
                        'changeemail'    => [
                            'type'    => Literal::class,
                            'options' => [
                                'route'    => '/change-email',
                                'defaults' => [
                                    'controller' => 'lmcuser',
                                    'action'     => 'changeemail',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
