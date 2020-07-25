<?php

namespace LmcUser\Factory\Controller;

use Interop\Container\ContainerInterface;
use Laminas\Mvc\Controller\ControllerManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LmcUser\Controller\RedirectCallback;
use LmcUser\Controller\UserController;

class UserControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        /* @var RedirectCallback $redirectCallback */
        $redirectCallback = $serviceManager->get('lmcuser_redirect_callback');

        /* @var UserController $controller */
        $controller = new UserController($redirectCallback);
        $controller->setServiceLocator($serviceManager);

        $controller->setChangeEmailForm($serviceManager->get('lmcuser_change_email_form'));
        $controller->setOptions($serviceManager->get('lmcuser_module_options'));
        $controller->setChangePasswordForm($serviceManager->get('lmcuser_change_password_form'));
        $controller->setLoginForm($serviceManager->get('lmcuser_login_form'));
        $controller->setRegisterForm($serviceManager->get('lmcuser_register_form'));
        $controller->setUserService($serviceManager->get('lmcuser_user_service'));

        return $controller;
    }

    /**
     * Create service
     *
     * @param  ServiceLocatorInterface $controllerManager
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $controllerManager)
    {
        /* @var ControllerManager $controllerManager*/
        $serviceManager = $controllerManager->getServiceLocator();

        return $this->__invoke($serviceManager, null);
    }
}
