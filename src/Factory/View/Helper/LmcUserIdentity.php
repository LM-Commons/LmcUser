<?php

namespace LmcUser\Factory\View\Helper;

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use LmcUser\View;

class LmcUserIdentity implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $viewHelper = new View\Helper\LmcUserIdentity;
        $viewHelper->setAuthService($container->get('lmcuser_auth_service'));

        return $viewHelper;
    }
}
