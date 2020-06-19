<?php
namespace LaminasUserTest\Factory\Form;

use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\ServiceManager;
use LaminasUser\Factory\Form\ChangeEmail as ChangeEmailFactory;
use LaminasUser\Options\ModuleOptions;
use LaminasUser\Mapper\User as UserMapper;

class ChangeEmailFormFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $serviceManager = new ServiceManager([
            'services' => [
                'laminasuser_module_options' => new ModuleOptions,
                'laminasuser_user_mapper' => new UserMapper
            ]
        ]);

        $formElementManager = new FormElementManager($serviceManager);
        $serviceManager->setService('FormElementManager', $formElementManager);

        $factory = new ChangeEmailFactory();

        $this->assertInstanceOf('LaminasUser\Form\ChangeEmail', $factory->__invoke($serviceManager, 'LaminasUser\Form\ChangeEmail'));
    }
}
