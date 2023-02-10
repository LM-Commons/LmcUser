<?php

namespace LmcUserTest\Service;

use LmcUser\Service\User as Service;
use Laminas\Crypt\Password\Bcrypt;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    protected $service;

    protected $options;

    protected $serviceManager;

    protected $formHydrator;

    protected $eventManager;

    protected $mapper;

    protected $authService;

    public function setUp():void
    {
        $service = new Service;
        $this->service = $service;

        $options = $this->createMock('LmcUser\Options\ModuleOptions');
        $this->options = $options;

        $serviceManager = $this->createMock('Laminas\ServiceManager\ServiceManager');
        $this->serviceManager = $serviceManager;

        $eventManager = $this->createMock('Laminas\EventManager\EventManager');
        $this->eventManager = $eventManager;

        $formHydrator = $this->createMock('Laminas\Hydrator\HydratorInterface');
        $this->formHydrator = $formHydrator;

        $mapper = $this->createMock('LmcUser\Mapper\UserInterface');
        $this->mapper = $mapper;

        $authService = $this->getMockBuilder('Laminas\Authentication\AuthenticationService')->disableOriginalConstructor()->getMock();
        $this->authService = $authService;

        $service->setOptions($options);
        $service->setServiceManager($serviceManager);
        $service->setFormHydrator($formHydrator);
        $service->setEventManager($eventManager);
        $service->setUserMapper($mapper);
        $service->setAuthService($authService);
    }

    /**
     * @covers LmcUser\Service\User::register
     */
    public function testRegisterWithInvalidForm()
    {
        $expectArray = array('username' => 'LmcUser');

        $this->options->expects($this->once())
            ->method('getUserEntityClass')
            ->will($this->returnValue('LmcUser\Entity\User'));

        $registerForm = $this->getMockBuilder('LmcUser\Form\Register')->disableOriginalConstructor()->getMock();
        $registerForm->expects($this->once())
            ->method('setHydrator');
        $registerForm->expects($this->once())
            ->method('bind');
        $registerForm->expects($this->once())
            ->method('setData')
            ->with($expectArray);
        $registerForm->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));

        $this->service->setRegisterForm($registerForm);

        $result = $this->service->register($expectArray);

        $this->assertFalse($result);
    }

    /**
     * @covers LmcUser\Service\User::register
     */
    public function testRegisterWithUsernameAndDisplayNameUserStateDisabled()
    {
        $expectArray = array('username' => 'LmcUser', 'display_name' => 'Zfc User');

        $user = $this->createMock('LmcUser\Entity\User');
        $user->expects($this->once())
            ->method('setPassword');
        $user->expects($this->once())
            ->method('getPassword');
        $user->expects($this->once())
            ->method('setUsername')
            ->with('LmcUser');
        $user->expects($this->once())
            ->method('setDisplayName')
            ->with('Zfc User');
        $user->expects($this->once())
            ->method('setState')
            ->with(1);

        $this->options->expects($this->once())
            ->method('getUserEntityClass')
            ->will($this->returnValue('LmcUser\Entity\User'));
        $this->options->expects($this->once())
            ->method('getPasswordCost')
            ->will($this->returnValue(4));
        $this->options->expects($this->once())
            ->method('getEnableUsername')
            ->will($this->returnValue(true));
        $this->options->expects($this->once())
            ->method('getEnableDisplayName')
            ->will($this->returnValue(true));
        $this->options->expects($this->once())
            ->method('getEnableUserState')
            ->will($this->returnValue(true));
        $this->options->expects($this->once())
            ->method('getDefaultUserState')
            ->will($this->returnValue(1));

        $registerForm = $this->getMockBuilder('LmcUser\Form\Register')->disableOriginalConstructor()->getMock();
        $registerForm->expects($this->once())
            ->method('setHydrator');
        $registerForm->expects($this->once())
            ->method('bind');
        $registerForm->expects($this->once())
            ->method('setData')
            ->with($expectArray);
        $registerForm->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($user));
        $registerForm->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->eventManager->expects($this->exactly(2))
            ->method('trigger');

        $this->mapper->expects($this->once())
            ->method('insert')
            ->with($user)
            ->will($this->returnValue($user));

        $this->service->setRegisterForm($registerForm);

        $result = $this->service->register($expectArray);

        $this->assertSame($user, $result);
    }

    /**
     * @covers LmcUser\Service\User::register
     */
    public function testRegisterWithDefaultUserStateOfZero()
    {
        $expectArray = array('username' => 'LmcUser', 'display_name' => 'Zfc User');

        $user = $this->createMock('LmcUser\Entity\User');
        $user->expects($this->once())
            ->method('setPassword');
        $user->expects($this->once())
            ->method('getPassword');
        $user->expects($this->once())
            ->method('setUsername')
            ->with('LmcUser');
        $user->expects($this->once())
            ->method('setDisplayName')
            ->with('Zfc User');
        $user->expects($this->once())
            ->method('setState')
            ->with(0);

        $this->options->expects($this->once())
            ->method('getUserEntityClass')
            ->will($this->returnValue('LmcUser\Entity\User'));
        $this->options->expects($this->once())
            ->method('getPasswordCost')
            ->will($this->returnValue(4));
        $this->options->expects($this->once())
            ->method('getEnableUsername')
            ->will($this->returnValue(true));
        $this->options->expects($this->once())
            ->method('getEnableDisplayName')
            ->will($this->returnValue(true));
        $this->options->expects($this->once())
            ->method('getEnableUserState')
            ->will($this->returnValue(true));
        $this->options->expects($this->once())
            ->method('getDefaultUserState')
            ->will($this->returnValue(0));

        $registerForm = $this->getMockBuilder('LmcUser\Form\Register')->disableOriginalConstructor()->getMock();
        $registerForm->expects($this->once())
            ->method('setHydrator');
        $registerForm->expects($this->once())
            ->method('bind');
        $registerForm->expects($this->once())
            ->method('setData')
            ->with($expectArray);
        $registerForm->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($user));
        $registerForm->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->eventManager->expects($this->exactly(2))
            ->method('trigger');

        $this->mapper->expects($this->once())
            ->method('insert')
            ->with($user)
            ->will($this->returnValue($user));

        $this->service->setRegisterForm($registerForm);

        $result = $this->service->register($expectArray);

        $this->assertSame($user, $result);
        $this->assertEquals(0, $user->getState());
    }

    /**
     * @covers LmcUser\Service\User::register
     */
    public function testRegisterWithUserStateDisabled()
    {
        $expectArray = array('username' => 'LmcUser', 'display_name' => 'Zfc User');

        $user = $this->createMock('LmcUser\Entity\User');
        $user->expects($this->once())
            ->method('setPassword');
        $user->expects($this->once())
            ->method('getPassword');
        $user->expects($this->once())
            ->method('setUsername')
            ->with('LmcUser');
        $user->expects($this->once())
            ->method('setDisplayName')
            ->with('Zfc User');
        $user->expects($this->never())
            ->method('setState');

        $this->options->expects($this->once())
            ->method('getUserEntityClass')
            ->will($this->returnValue('LmcUser\Entity\User'));
        $this->options->expects($this->once())
            ->method('getPasswordCost')
            ->will($this->returnValue(4));
        $this->options->expects($this->once())
            ->method('getEnableUsername')
            ->will($this->returnValue(true));
        $this->options->expects($this->once())
            ->method('getEnableDisplayName')
            ->will($this->returnValue(true));
        $this->options->expects($this->once())
            ->method('getEnableUserState')
            ->will($this->returnValue(false));
        $this->options->expects($this->never())
            ->method('getDefaultUserState');

        $registerForm = $this->getMockBuilder('LmcUser\Form\Register')->disableOriginalConstructor()->getMock();
        $registerForm->expects($this->once())
            ->method('setHydrator');
        $registerForm->expects($this->once())
            ->method('bind');
        $registerForm->expects($this->once())
            ->method('setData')
            ->with($expectArray);
        $registerForm->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($user));
        $registerForm->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->eventManager->expects($this->exactly(2))
            ->method('trigger');

        $this->mapper->expects($this->once())
            ->method('insert')
            ->with($user)
            ->will($this->returnValue($user));

        $this->service->setRegisterForm($registerForm);

        $result = $this->service->register($expectArray);

        $this->assertSame($user, $result);
        $this->assertEquals(0, $user->getState());
    }
    
    /**
     * @covers LmcUser\Service\User::changePassword
     */
    public function testChangePasswordWithWrongOldPassword()
    {
        $data = array('newCredential' => 'lmcUser', 'credential' => 'lmcUserOld');

        $this->options->expects($this->any())
            ->method('getPasswordCost')
            ->will($this->returnValue(4));

        $bcrypt = new Bcrypt();
        $bcrypt->setCost($this->options->getPasswordCost());

        $user = $this->createMock('LmcUser\Entity\User');
        $user->expects($this->any())
            ->method('getPassword')
            ->will($this->returnValue($bcrypt->create('wrongPassword')));

        $this->authService->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($user));

        $result = $this->service->changePassword($data);
        $this->assertFalse($result);
    }

    /**
     * @covers LmcUser\Service\User::changePassword
     */
    public function testChangePassword()
    {
        $data = array('newCredential' => 'lmcUser', 'credential' => 'lmcUserOld');

        $this->options->expects($this->any())
            ->method('getPasswordCost')
            ->will($this->returnValue(4));

        $bcrypt = new Bcrypt();
        $bcrypt->setCost($this->options->getPasswordCost());

        $user = $this->createMock('LmcUser\Entity\User');
        $user->expects($this->any())
            ->method('getPassword')
            ->will($this->returnValue($bcrypt->create($data['credential'])));
        $user->expects($this->any())
            ->method('setPassword');

        $this->authService->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($user));

        $this->eventManager->expects($this->exactly(2))
            ->method('trigger');

        $this->mapper->expects($this->once())
            ->method('update')
            ->with($user);

        $result = $this->service->changePassword($data);
        $this->assertTrue($result);
    }

    /**
     * @covers LmcUser\Service\User::changeEmail
     */
    public function testChangeEmail()
    {
        $data = array('credential' => 'lmcUser', 'newIdentity' => 'lmcUser@lmcUser.com');

        $this->options->expects($this->any())
            ->method('getPasswordCost')
            ->will($this->returnValue(4));

        $bcrypt = new Bcrypt();
        $bcrypt->setCost($this->options->getPasswordCost());

        $user = $this->createMock('LmcUser\Entity\User');
        $user->expects($this->any())
            ->method('getPassword')
            ->will($this->returnValue($bcrypt->create($data['credential'])));
        $user->expects($this->any())
            ->method('setEmail')
            ->with('lmcUser@lmcUser.com');

        $this->authService->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($user));

        $this->eventManager->expects($this->exactly(2))
            ->method('trigger');

        $this->mapper->expects($this->once())
            ->method('update')
            ->with($user);

        $result = $this->service->changeEmail($data);
        $this->assertTrue($result);
    }

    /**
     * @covers LmcUser\Service\User::changeEmail
     */
    public function testChangeEmailWithWrongPassword()
    {
        $data = array('credential' => 'lmcUserOld');

        $this->options->expects($this->any())
            ->method('getPasswordCost')
            ->will($this->returnValue(4));

        $bcrypt = new Bcrypt();
        $bcrypt->setCost($this->options->getPasswordCost());

        $user = $this->createMock('LmcUser\Entity\User');
        $user->expects($this->any())
            ->method('getPassword')
            ->will($this->returnValue($bcrypt->create('wrongPassword')));

        $this->authService->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($user));

        $result = $this->service->changeEmail($data);
        $this->assertFalse($result);
    }

    /**
     * @covers LmcUser\Service\User::getUserMapper
     */
    public function testGetUserMapper()
    {
        $this->serviceManager->expects($this->once())
            ->method('get')
            ->with('lmcuser_user_mapper')
            ->will($this->returnValue($this->mapper));

        $service = new Service;
        $service->setServiceManager($this->serviceManager);
        $this->assertInstanceOf('LmcUser\Mapper\UserInterface', $service->getUserMapper());
    }

    /**
     * @covers LmcUser\Service\User::getUserMapper
     * @covers LmcUser\Service\User::setUserMapper
     */
    public function testSetGetUserMapper()
    {
        $this->assertSame($this->mapper, $this->service->getUserMapper());
    }

    /**
     * @covers LmcUser\Service\User::getAuthService
     */
    public function testGetAuthService()
    {
        $this->serviceManager->expects($this->once())
            ->method('get')
            ->with('lmcuser_auth_service')
            ->will($this->returnValue($this->authService));

        $service = new Service;
        $service->setServiceManager($this->serviceManager);
        $this->assertInstanceOf('Laminas\Authentication\AuthenticationService', $service->getAuthService());
    }

    /**
     * @covers LmcUser\Service\User::getAuthService
     * @covers LmcUser\Service\User::setAuthService
     */
    public function testSetGetAuthService()
    {
        $this->assertSame($this->authService, $this->service->getAuthService());
    }

    /**
     * @covers LmcUser\Service\User::getRegisterForm
     */
    public function testGetRegisterForm()
    {
        $form = $this->getMockBuilder('LmcUser\Form\Register')->disableOriginalConstructor()->getMock();

        $this->serviceManager->expects($this->once())
            ->method('get')
            ->with('lmcuser_register_form')
            ->will($this->returnValue($form));

        $service = new Service;
        $service->setServiceManager($this->serviceManager);

        $result = $service->getRegisterForm();

        $this->assertInstanceOf('LmcUser\Form\Register', $result);
        $this->assertSame($form, $result);
    }

    /**
     * @covers LmcUser\Service\User::getRegisterForm
     * @covers LmcUser\Service\User::setRegisterForm
     */
    public function testSetGetRegisterForm()
    {
        $form = $this->getMockBuilder('LmcUser\Form\Register')->disableOriginalConstructor()->getMock();
        $this->service->setRegisterForm($form);

        $this->assertSame($form, $this->service->getRegisterForm());
    }

    /**
     * @covers LmcUser\Service\User::getChangePasswordForm
     */
    public function testGetChangePasswordForm()
    {
        $form = $this->getMockBuilder('LmcUser\Form\ChangePassword')->disableOriginalConstructor()->getMock();

        $this->serviceManager->expects($this->once())
            ->method('get')
            ->with('lmcuser_change_password_form')
            ->will($this->returnValue($form));

        $service = new Service;
        $service->setServiceManager($this->serviceManager);
        $this->assertInstanceOf('LmcUser\Form\ChangePassword', $service->getChangePasswordForm());
    }

    /**
     * @covers LmcUser\Service\User::getChangePasswordForm
     * @covers LmcUser\Service\User::setChangePasswordForm
     */
    public function testSetGetChangePasswordForm()
    {
        $form = $this->getMockBuilder('LmcUser\Form\ChangePassword')->disableOriginalConstructor()->getMock();
        $this->service->setChangePasswordForm($form);

        $this->assertSame($form, $this->service->getChangePasswordForm());
    }

    /**
     * @covers LmcUser\Service\User::getOptions
     */
    public function testGetOptions()
    {
        $this->serviceManager->expects($this->once())
            ->method('get')
            ->with('lmcuser_module_options')
            ->will($this->returnValue($this->options));

        $service = new Service;
        $service->setServiceManager($this->serviceManager);
        $this->assertInstanceOf('LmcUser\Options\ModuleOptions', $service->getOptions());
    }

    /**
     * @covers LmcUser\Service\User::setOptions
     */
    public function testSetOptions()
    {
        $this->assertSame($this->options, $this->service->getOptions());
    }

    /**
     * @covers LmcUser\Service\User::getServiceManager
     * @covers LmcUser\Service\User::setServiceManager
     */
    public function testSetGetServiceManager()
    {
        $this->assertSame($this->serviceManager, $this->service->getServiceManager());
    }

    /**
     * @covers LmcUser\Service\User::getFormHydrator
     */
    public function testGetFormHydrator()
    {
        $this->serviceManager->expects($this->once())
            ->method('get')
            ->with('lmcuser_register_form_hydrator')
            ->will($this->returnValue($this->formHydrator));

        $service = new Service;
        $service->setServiceManager($this->serviceManager);
        $this->assertInstanceOf('Laminas\Hydrator\HydratorInterface', $service->getFormHydrator());
    }

    /**
     * @covers LmcUser\Service\User::setFormHydrator
     */
    public function testSetFormHydrator()
    {
        $this->assertSame($this->formHydrator, $this->service->getFormHydrator());
    }
}
