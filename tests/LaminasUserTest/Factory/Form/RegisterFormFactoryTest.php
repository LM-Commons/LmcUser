<?php
namespace LaminasUserTest\Factory\Form;

use Zend\Form\FormElementManager;
use Zend\ServiceManager\ServiceManager;
use Zend\Hydrator\ClassMethods;
use LaminasUser\Factory\Form\Register as RegisterFactory;
use LaminasUser\Options\ModuleOptions;
use LaminasUser\Mapper\User as UserMapper;

class RegisterFormFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $serviceManager = new ServiceManager;
        $serviceManager->setService('zfcuser_module_options', new ModuleOptions);
        $serviceManager->setService('zfcuser_user_mapper', new UserMapper);
        $serviceManager->setService('zfcuser_register_form_hydrator', new ClassMethods());

        $formElementManager = new FormElementManager($serviceManager);
        $serviceManager->setService('FormElementManager', $formElementManager);

        $factory = new RegisterFactory();

        $this->assertInstanceOf('LaminasUser\Form\Register', $factory->__invoke($serviceManager, 'LaminasUser\Form\Register'));
    }
}
