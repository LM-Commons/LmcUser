<?php

namespace LmcUser\Factory\Controller;

use Psr\Container\ContainerInterface;
use Laminas\Mvc\Application;
use Laminas\Router\RouteInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LmcUser\Controller\RedirectCallback;
use LmcUser\Options\ModuleOptions;

class RedirectCallbackFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        /* @var RouteInterface $router */
        $router = $serviceLocator->get('Router');

        /* @var Application $application */
        $application = $serviceLocator->get('Application');

        /* @var ModuleOptions $options */
        $options = $serviceLocator->get('lmcuser_module_options');

        return new RedirectCallback($application, $router, $options);
    }

    /**
     * Create service
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this->__invoke($serviceLocator, null);
    }
}
