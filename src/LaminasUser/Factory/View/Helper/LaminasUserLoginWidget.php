<?php

namespace LmcUser\Factory\View\Helper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use LmcUser\View;

class LmcUserLoginWidget implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $viewHelper = new View\Helper\LmcUserLoginWidget;
        $viewHelper->setViewTemplate($container->get('laminasuser_module_options')->getUserLoginWidgetViewTemplate());
        $viewHelper->setLoginForm($container->get('laminasuser_login_form'));

        return $viewHelper;
    }
}
