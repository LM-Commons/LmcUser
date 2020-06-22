<?php

namespace LmcUser\Factory\View\Helper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use LmcUser\View;

class LmcUserDisplayName implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $viewHelper = new View\Helper\LmcUserDisplayName;
        $viewHelper->setAuthService($container->get('lmcuser_auth_service'));

        return $viewHelper;
    }
}
