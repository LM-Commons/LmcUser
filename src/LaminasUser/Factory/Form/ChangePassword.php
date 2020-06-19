<?php

namespace LaminasUser\Factory\Form;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use LaminasUser\Form;

class ChangePassword implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        $options = $serviceManager->get('laminasuser_module_options');
        $form = new Form\ChangePassword(null, $options);

        $form->setInputFilter(new Form\ChangePasswordFilter($options));

        return $form;
    }
}
