<?php
namespace LmcUserTest\Factory\Form;

use Laminas\Form\FormElementManager;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ServiceManager\ServiceManager;
use LmcUser\Factory\Form\Register as RegisterFactory;
use LmcUser\Options\ModuleOptions;
use LmcUser\Mapper\User as UserMapper;
use PHPUnit\Framework\TestCase;

class RegisterFormFactoryTest extends TestCase
{
    public function testFactory()
    {
        $serviceManager = new ServiceManager;
        $serviceManager->setService('lmcuser_module_options', new ModuleOptions);
        $serviceManager->setService('lmcuser_user_mapper', new UserMapper);
        $serviceManager->setService('lmcuser_register_form_hydrator', new ClassMethodsHydrator());

        $formElementManager = new FormElementManager($serviceManager);
        $serviceManager->setService('FormElementManager', $formElementManager);

        $factory = new RegisterFactory();

        $this->assertInstanceOf('LmcUser\Form\Register', $factory->__invoke($serviceManager, 'LmcUser\Form\Register'));
    }
}
