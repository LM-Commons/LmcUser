<?php

namespace LmcUser\Factory\Form;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use LmcUser\Form;

class Otp implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        $options = $serviceManager->get('lmcuser_module_options');
        $form = new Form\Otp(null, $options);

        $form->setInputFilter(new Form\OtpFilter($options));

        return $form;
    }
}
