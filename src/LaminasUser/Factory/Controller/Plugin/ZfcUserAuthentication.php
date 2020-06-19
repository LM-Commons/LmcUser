<?php

namespace LaminasUser\Factory\Controller\Plugin;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LaminasUser\Controller;

class LaminasUserAuthentication implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $authService = $serviceLocator->get('laminasuser_auth_service');
        $authAdapter = $serviceLocator->get('LaminasUser\Authentication\Adapter\AdapterChain');

        $controllerPlugin = new Controller\Plugin\LaminasUserAuthentication;
        $controllerPlugin->setAuthService($authService);
        $controllerPlugin->setAuthAdapter($authAdapter);

        return $controllerPlugin;
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceManager
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceManager)
    {
        $serviceLocator = $serviceManager->getServiceLocator();

        return $this->__invoke($serviceLocator, null);
    }
}
