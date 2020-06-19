<?php
namespace LaminasUserTest\Factory\Form;

use Zend\Form\FormElementManager;
use Zend\ServiceManager\ServiceManager;
use LaminasUser\Factory\Form\ChangePassword as ChangePasswordFactory;
use LaminasUser\Options\ModuleOptions;
use LaminasUser\Mapper\User as UserMapper;

class ChangePasswordFormFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $serviceManager = new ServiceManager;
        $serviceManager->setService('zfcuser_module_options', new ModuleOptions);
        $serviceManager->setService('zfcuser_user_mapper', new UserMapper);

        $formElementManager = new FormElementManager($serviceManager);
        $serviceManager->setService('FormElementManager', $formElementManager);

        $factory = new ChangePasswordFactory();

        $this->assertInstanceOf('LaminasUser\Form\ChangePassword', $factory->__invoke($serviceManager, 'LaminasUser\Form\ChangePassword'));
    }
}
