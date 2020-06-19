<?php

namespace LaminasUser\Factory\View\Helper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use LaminasUser\View;

class LaminasUserIdentity implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $viewHelper = new View\Helper\LaminasUserIdentity;
        $viewHelper->setAuthService($container->get('laminasuser_auth_service'));

        return $viewHelper;
    }
}
