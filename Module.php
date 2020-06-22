<?php

namespace LmcUser;

use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\ControllerPluginProviderInterface;
use Laminas\ModuleManager\Feature\ControllerProviderInterface;
use Laminas\ModuleManager\Feature\ServiceProviderInterface;

class Module implements
    ControllerProviderInterface,
    ControllerPluginProviderInterface,
    ConfigProviderInterface,
    ServiceProviderInterface
{
    public function getConfig($env = null)
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getControllerPluginConfig()
    {
        return array(
            'factories' => array(
                'laminasUserAuthentication' => \LmcUser\Factory\Controller\Plugin\LmcUserAuthentication::class,
            ),
        );
    }

    public function getControllerConfig()
    {
        return array(
            'factories' => array(
                'laminasuser' => \LmcUser\Factory\Controller\UserControllerFactory::class,
            ),
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'laminasUserDisplayName' => \LmcUser\Factory\View\Helper\LmcUserDisplayName::class,
                'laminasUserIdentity' => \LmcUser\Factory\View\Helper\LmcUserIdentity::class,
                'laminasUserLoginWidget' => \LmcUser\Factory\View\Helper\LmcUserLoginWidget::class,
            ),
        );

    }

    public function getServiceConfig()
    {
        return array(
            'aliases' => array(
                'laminasuser_laminas_db_adapter' => \Laminas\Db\Adapter\Adapter::class,
            ),
            'invokables' => array(
                'laminasuser_register_form_hydrator' => \Laminas\Hydrator\ClassMethods::class,
            ),
            'factories' => array(
                'laminasuser_redirect_callback' => \LmcUser\Factory\Controller\RedirectCallbackFactory::class,
                'laminasuser_module_options' => \LmcUser\Factory\Options\ModuleOptions::class,
                'LmcUser\Authentication\Adapter\AdapterChain' => \LmcUser\Authentication\Adapter\AdapterChainServiceFactory::class,

                // We alias this one because it's LmcUser's instance of
                // Laminas\Authentication\AuthenticationService. We don't want to
                // hog the FQCN service alias for a Laminas\* class.
                'laminasuser_auth_service' => \LmcUser\Factory\AuthenticationService::class,

                'laminasuser_user_hydrator' => \LmcUser\Factory\UserHydrator::class,
                'laminasuser_user_mapper' => \LmcUser\Factory\Mapper\User::class,

                'laminasuser_login_form' => \LmcUser\Factory\Form\Login::class,
                'laminasuser_register_form' => \LmcUser\Factory\Form\Register::class,
                'laminasuser_change_password_form' => \LmcUser\Factory\Form\ChangePassword::class,
                'laminasuser_change_email_form' => \LmcUser\Factory\Form\ChangeEmail::class,

                'LmcUser\Authentication\Adapter\Db' => \LmcUser\Factory\Authentication\Adapter\DbFactory::class,
                'LmcUser\Authentication\Storage\Db' => \LmcUser\Factory\Authentication\Storage\DbFactory::class,

                'laminasuser_user_service' => \LmcUser\Factory\Service\UserFactory::class,
            ),
        );
    }
}
