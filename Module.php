<?php

namespace LaminasUser;

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
                'laminasUserAuthentication' => \LaminasUser\Factory\Controller\Plugin\LaminasUserAuthentication::class,
            ),
        );
    }

    public function getControllerConfig()
    {
        return array(
            'factories' => array(
                'laminasuser' => \LaminasUser\Factory\Controller\UserControllerFactory::class,
            ),
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'laminasUserDisplayName' => \LaminasUser\Factory\View\Helper\LaminasUserDisplayName::class,
                'laminasUserIdentity' => \LaminasUser\Factory\View\Helper\LaminasUserIdentity::class,
                'laminasUserLoginWidget' => \LaminasUser\Factory\View\Helper\LaminasUserLoginWidget::class,
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
                'laminasuser_redirect_callback' => \LaminasUser\Factory\Controller\RedirectCallbackFactory::class,
                'laminasuser_module_options' => \LaminasUser\Factory\Options\ModuleOptions::class,
                'LaminasUser\Authentication\Adapter\AdapterChain' => \LaminasUser\Authentication\Adapter\AdapterChainServiceFactory::class,

                // We alias this one because it's LaminasUser's instance of
                // Laminas\Authentication\AuthenticationService. We don't want to
                // hog the FQCN service alias for a Laminas\* class.
                'laminasuser_auth_service' => \LaminasUser\Factory\AuthenticationService::class,

                'laminasuser_user_hydrator' => \LaminasUser\Factory\UserHydrator::class,
                'laminasuser_user_mapper' => \LaminasUser\Factory\Mapper\User::class,

                'laminasuser_login_form' => \LaminasUser\Factory\Form\Login::class,
                'laminasuser_register_form' => \LaminasUser\Factory\Form\Register::class,
                'laminasuser_change_password_form' => \LaminasUser\Factory\Form\ChangePassword::class,
                'laminasuser_change_email_form' => \LaminasUser\Factory\Form\ChangeEmail::class,

                'LaminasUser\Authentication\Adapter\Db' => \LaminasUser\Factory\Authentication\Adapter\DbFactory::class,
                'LaminasUser\Authentication\Storage\Db' => \LaminasUser\Factory\Authentication\Storage\DbFactory::class,

                'laminasuser_user_service' => \LaminasUser\Factory\Service\UserFactory::class,
            ),
        );
    }
}
