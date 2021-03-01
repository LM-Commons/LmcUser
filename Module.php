<?php

namespace LmcUser;

use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\ControllerPluginProviderInterface;
use Laminas\ModuleManager\Feature\ControllerProviderInterface;
use Laminas\ModuleManager\Feature\ServiceProviderInterface;
use Laminas\ModuleManager\Feature\ViewHelperProviderInterface;

/**
 * Class Module
 */
class Module implements
    ConfigProviderInterface,
    ControllerPluginProviderInterface,
    ControllerProviderInterface,
    ServiceProviderInterface,
    ViewHelperProviderInterface
{
    public const LMC_USER_SESSION_STORAGE_NAMESPACE = 'LmcUserNamespace';

    /**
     * {@inheritDoc}
     * @see \Laminas\ModuleManager\Feature\ConfigProviderInterface::getConfig()
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * {@inheritDoc}
     * @see \Laminas\ModuleManager\Feature\ControllerPluginProviderInterface::getControllerPluginConfig()
     */
    public function getControllerPluginConfig()
    {
        return [
            'factories' => [
                'lmcUserAuthentication' => Factory\Controller\Plugin\LmcUserAuthentication::class,
            ],
        ];
    }

    /**
     * {@inheritDoc}
     * @see \Laminas\ModuleManager\Feature\ControllerProviderInterface::getControllerConfig()
     */
    public function getControllerConfig()
    {
        return [
            'factories' => [
                'lmcuser' => Factory\Controller\UserControllerFactory::class,
            ],
        ];
    }

    /**
     * {@inheritDoc}
     * @see \Laminas\ModuleManager\Feature\ViewHelperProviderInterface::getViewHelperConfig()
     */
    public function getViewHelperConfig()
    {
        return [
            'factories' => [
                'lmcUserDisplayName' => Factory\View\Helper\LmcUserDisplayName::class,
                'lmcUserIdentity' => Factory\View\Helper\LmcUserIdentity::class,
                'lmcUserLoginWidget' => Factory\View\Helper\LmcUserLoginWidget::class,
            ],
        ];
    }

    /**
     * {@inheritDoc}
     * @see \Laminas\ModuleManager\Feature\ServiceProviderInterface::getServiceConfig()
     */
    public function getServiceConfig()
    {
        return [
            'aliases' => [
                'lmcuser_laminas_db_adapter' => \Laminas\Db\Adapter\Adapter::class,
                'lmcuser_register_form_hydrator' => 'lmcuser_user_hydrator',
                'lmcuser_base_hydrator' => 'lmcuser_default_hydrator',
            ],
            'invokables' => [
                'lmcuser_default_hydrator' => ClassMethodsHydrator::class,
            ],
            'factories' => [
                'lmcuser_redirect_callback' => Factory\Controller\RedirectCallbackFactory::class,
                'lmcuser_module_options' => Factory\Options\ModuleOptions::class,
                Authentication\Adapter\AdapterChain::class => Authentication\Adapter\AdapterChainServiceFactory::class,

                // We alias this one because it's LmcUser's instance of
                // Laminas\Authentication\AuthenticationService. We don't want to
                // hog the FQCN service alias for a Laminas\* class.
                'lmcuser_auth_service' => Factory\AuthenticationService::class,

                'lmcuser_user_hydrator' => Factory\UserHydrator::class,
                'lmcuser_user_mapper' => Factory\Mapper\User::class,

                'lmcuser_login_form' => Factory\Form\Login::class,
                'lmcuser_register_form' => Factory\Form\Register::class,
                'lmcuser_change_password_form' => Factory\Form\ChangePassword::class,
                'lmcuser_change_email_form' => Factory\Form\ChangeEmail::class,

                Authentication\Adapter\Db::class => Factory\Authentication\Adapter\DbFactory::class,
                Authentication\Storage\Db::class => Factory\Authentication\Storage\DbFactory::class,

                'lmcuser_user_service' => Factory\Service\UserFactory::class,
            ],
        ];
    }
}
