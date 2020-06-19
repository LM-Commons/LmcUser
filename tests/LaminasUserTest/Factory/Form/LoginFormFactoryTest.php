<?php
namespace LaminasUserTest\Factory\Form;

use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\ServiceManager;
use LaminasUser\Factory\Form\Login as LoginFactory;
use LaminasUser\Options\ModuleOptions;

class LoginFormFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $serviceManager = new ServiceManager;
        $serviceManager->setService('laminasuser_module_options', new ModuleOptions);

        $formElementManager = new FormElementManager($serviceManager);
        $serviceManager->setService('FormElementManager', $formElementManager);

        $factory = new LoginFactory();

        $this->assertInstanceOf('LaminasUser\Form\Login', $factory->__invoke($serviceManager, 'LaminasUser\Form\Login'));
    }
}
