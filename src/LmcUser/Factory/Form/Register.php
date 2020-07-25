<?php

namespace LmcUser\Factory\Form;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use LmcUser\Form;
use LmcUser\Validator;

class Register implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        $options = $serviceManager->get('lmcuser_module_options');
        $form = new Form\Register(null, $options);

        //$form->setCaptchaElement($sm->get('lmcuser_captcha_element'));
        $form->setHydrator($serviceManager->get('lmcuser_register_form_hydrator'));
        $form->setInputFilter(
            new Form\RegisterFilter(
                new Validator\NoRecordExists(
                    array(
                    'mapper' => $serviceManager->get('lmcuser_user_mapper'),
                    'key'    => 'email'
                    )
                ),
                new Validator\NoRecordExists(
                    array(
                    'mapper' => $serviceManager->get('lmcuser_user_mapper'),
                    'key'    => 'username'
                    )
                ),
                $options
            )
        );

        return $form;
    }
}
