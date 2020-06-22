<?php
namespace LmcUserTest\Factory\Form;

use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\ServiceManager;
use LmcUser\Factory\Form\Login as LoginFactory;
use LmcUser\Options\ModuleOptions;

class LoginFormFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $serviceManager = new ServiceManager;
        $serviceManager->setService('lmcuser_module_options', new ModuleOptions);

        $formElementManager = new FormElementManager($serviceManager);
        $serviceManager->setService('FormElementManager', $formElementManager);

        $factory = new LoginFactory();

        $this->assertInstanceOf('LmcUser\Form\Login', $factory->__invoke($serviceManager, 'LmcUser\Form\Login'));
    }
}
