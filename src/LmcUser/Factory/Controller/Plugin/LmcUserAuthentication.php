<?php

namespace LmcUser\Factory\Controller\Plugin;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LmcUser\Controller;

class LmcUserAuthentication implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $authService = $serviceLocator->get('lmcuser_auth_service');
        $authAdapter = $serviceLocator->get('LmcUser\Authentication\Adapter\AdapterChain');

        $controllerPlugin = new Controller\Plugin\LmcUserAuthentication;
        $controllerPlugin->setAuthService($authService);
        $controllerPlugin->setAuthAdapter($authAdapter);

        return $controllerPlugin;
    }

    /**
     * Create service
     *
     * @param  ServiceLocatorInterface $serviceManager
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceManager)
    {
        $serviceLocator = $serviceManager->getServiceLocator();

        return $this->__invoke($serviceLocator, null);
    }
}
