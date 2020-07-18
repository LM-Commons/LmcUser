<?php
namespace LmcUserTest\Factory\Form;

use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\ServiceManager;
use LmcUser\Factory\Form\ChangeEmail as ChangeEmailFactory;
use LmcUser\Options\ModuleOptions;
use LmcUser\Mapper\User as UserMapper;
use PHPUnit\Framework\TestCase;

class ChangeEmailFormFactoryTest extends TestCase
{
    public function testFactory()
    {
        $serviceManager = new ServiceManager(
            [
            'services' => [
                'lmcuser_module_options' => new ModuleOptions,
                'lmcuser_user_mapper' => new UserMapper
            ]
            ]
        );

        $formElementManager = new FormElementManager($serviceManager);
        $serviceManager->setService('FormElementManager', $formElementManager);

        $factory = new ChangeEmailFactory();

        $this->assertInstanceOf('LmcUser\Form\ChangeEmail', $factory->__invoke($serviceManager, 'LmcUser\Form\ChangeEmail'));
    }
}
