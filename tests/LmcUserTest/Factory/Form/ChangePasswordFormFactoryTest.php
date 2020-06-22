<?php
namespace LmcUserTest\Factory\Form;

use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\ServiceManager;
use LmcUser\Factory\Form\ChangePassword as ChangePasswordFactory;
use LmcUser\Options\ModuleOptions;
use LmcUser\Mapper\User as UserMapper;

class ChangePasswordFormFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $serviceManager = new ServiceManager;
        $serviceManager->setService('lmcuser_module_options', new ModuleOptions);
        $serviceManager->setService('lmcuser_user_mapper', new UserMapper);

        $formElementManager = new FormElementManager($serviceManager);
        $serviceManager->setService('FormElementManager', $formElementManager);

        $factory = new ChangePasswordFactory();

        $this->assertInstanceOf('LmcUser\Form\ChangePassword', $factory->__invoke($serviceManager, 'LmcUser\Form\ChangePassword'));
    }
}
