<?php

namespace LaminasUser\Factory\View\Helper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use LaminasUser\View;

class LaminasUserLoginWidget implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $viewHelper = new View\Helper\LaminasUserLoginWidget;
        $viewHelper->setViewTemplate($container->get('laminasuser_module_options')->getUserLoginWidgetViewTemplate());
        $viewHelper->setLoginForm($container->get('laminasuser_login_form'));

        return $viewHelper;
    }
}
