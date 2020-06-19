<?php

namespace LaminasUserTest\Authentication\Adapter;

use Laminas\EventManager\Event;
use LaminasUser\Authentication\Adapter\Db;

class DbTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The object to be tested.
     *
     * @var Db
     */
    protected $db;

    /**
     * Mock of AuthEvent.
     *
     * @var \LaminasUser\Authentication\Adapter\AdapterChainEvent|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $authEvent;

    /**
     * Mock of Storage.
     *
     * @var \Laminas\Authentication\Storage\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storage;

    /**
     * Mock of Options.
     *
     * @var \LaminasUser\Options\ModuleOptions|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $options;

    /**
     * Mock of Mapper.
     *
     * @var \LaminasUser\Mapper\UserInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mapper;

    /**
     * Mock of User.
     *
     * @var \LaminasUser\Entity\UserInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $user;

    protected function setUp()
    {
        $storage = $this->getMock('Laminas\Authentication\Storage\Session');
        $this->storage = $storage;

        $authEvent = $this->getMock('LaminasUser\Authentication\Adapter\AdapterChainEvent');
        $this->authEvent = $authEvent;

        $options = $this->getMock('LaminasUser\Options\ModuleOptions');
        $this->options = $options;

        $mapper = $this->getMock('LaminasUser\Mapper\UserInterface');
        $this->mapper = $mapper;

        $user = $this->getMock('LaminasUser\Entity\UserInterface');
        $this->user = $user;

        $this->db = new Db;
        $this->db->setStorage($this->storage);

        $sessionManager = $this->getMock('Laminas\Session\SessionManager');
        \Laminas\Session\AbstractContainer::setDefaultManager($sessionManager);
    }

    /**
     * @covers \LaminasUser\Authentication\Adapter\Db::logout
     */
    public function testLogout()
    {
        $this->storage->expects($this->once())
                      ->method('clear');

         $this->db->logout($this->authEvent);
    }

    /**
     * @covers \LaminasUser\Authentication\Adapter\Db::Authenticate
     */
    public function testAuthenticateWhenSatisfies()
    {
        $this->authEvent->expects($this->once())
                        ->method('setIdentity')
                        ->with('LaminasUser')
                        ->will($this->returnValue($this->authEvent));
        $this->authEvent->expects($this->once())
                        ->method('setCode')
                        ->with(\Laminas\Authentication\Result::SUCCESS)
                        ->will($this->returnValue($this->authEvent));
        $this->authEvent->expects($this->once())
                        ->method('setMessages')
                        ->with(array('Authentication successful.'))
                        ->will($this->returnValue($this->authEvent));

        $this->storage->expects($this->at(0))
            ->method('read')
            ->will($this->returnValue(array('is_satisfied' => true)));
        $this->storage->expects($this->at(1))
            ->method('read')
            ->will($this->returnValue(array('identity' => 'LaminasUser')));

        $event = new Event(null, $this->authEvent);

        $result = $this->db->authenticate($event);
        $this->assertNull($result);
    }

    /**
     * @covers \LaminasUser\Authentication\Adapter\Db::Authenticate
     */
    public function testAuthenticateNoUserObject()
    {
        $this->setAuthenticationCredentials();

        $this->options->expects($this->once())
            ->method('getAuthIdentityFields')
            ->will($this->returnValue(array()));

        $this->authEvent->expects($this->once())
            ->method('setCode')
            ->with(\Laminas\Authentication\Result::FAILURE_IDENTITY_NOT_FOUND)
            ->will($this->returnValue($this->authEvent));
        $this->authEvent->expects($this->once())
            ->method('setMessages')
            ->with(array('A record with the supplied identity could not be found.'))
            ->will($this->returnValue($this->authEvent));

        $this->db->setOptions($this->options);

        $event = new Event(null, $this->authEvent);
        $result = $this->db->authenticate($event);

        $this->assertFalse($result);
        $this->assertFalse($this->db->isSatisfied());
    }

    /**
     * @covers \LaminasUser\Authentication\Adapter\Db::Authenticate
     */
    public function testAuthenticationUserStateEnabledUserButUserStateNotInArray()
    {
        $this->setAuthenticationCredentials();
        $this->setAuthenticationUser();

        $this->options->expects($this->once())
            ->method('getEnableUserState')
            ->will($this->returnValue(true));
        $this->options->expects($this->once())
            ->method('getAllowedLoginStates')
            ->will($this->returnValue(array(2, 3)));

        $this->authEvent->expects($this->once())
            ->method('setCode')
            ->with(\Laminas\Authentication\Result::FAILURE_UNCATEGORIZED)
            ->will($this->returnValue($this->authEvent));
        $this->authEvent->expects($this->once())
            ->method('setMessages')
            ->with(array('A record with the supplied identity is not active.'))
            ->will($this->returnValue($this->authEvent));

        $this->user->expects($this->once())
            ->method('getState')
            ->will($this->returnValue(1));

        $this->db->setMapper($this->mapper);
        $this->db->setOptions($this->options);

        $event = new Event(null, $this->authEvent);
        $result = $this->db->authenticate($event);

        $this->assertFalse($result);
        $this->assertFalse($this->db->isSatisfied());
    }

    /**
     * @covers \LaminasUser\Authentication\Adapter\Db::Authenticate
     */
    public function testAuthenticateWithWrongPassword()
    {
        $this->setAuthenticationCredentials();
        $this->setAuthenticationUser();

        $this->options->expects($this->once())
            ->method('getEnableUserState')
            ->will($this->returnValue(false));

        // Set lowest possible to spent the least amount of resources/time
        $this->options->expects($this->once())
            ->method('getPasswordCost')
            ->will($this->returnValue(4));

        $this->authEvent->expects($this->once())
            ->method('setCode')
            ->with(\Laminas\Authentication\Result::FAILURE_CREDENTIAL_INVALID)
            ->will($this->returnValue($this->authEvent));
        $this->authEvent->expects($this->once(1))
            ->method('setMessages')
            ->with(array('Supplied credential is invalid.'));

        $this->db->setMapper($this->mapper);
        $this->db->setOptions($this->options);

        $event = new Event(null, $this->authEvent);
        $result = $this->db->authenticate($event);

        $this->assertFalse($result);
        $this->assertFalse($this->db->isSatisfied());
    }

    /**
     * @covers \LaminasUser\Authentication\Adapter\Db::Authenticate
     */
    public function testAuthenticationAuthenticatesWithEmail()
    {
        $this->setAuthenticationCredentials('laminas-user@zf-commons.io');
        $this->setAuthenticationEmail();

        $this->options->expects($this->once())
            ->method('getEnableUserState')
            ->will($this->returnValue(false));

        $this->options->expects($this->once())
            ->method('getPasswordCost')
            ->will($this->returnValue(4));

        $this->user->expects($this->exactly(2))
            ->method('getPassword')
            ->will($this->returnValue('$2a$04$5kq1mnYWbww8X.rIj7eOVOHXtvGw/peefjIcm0lDGxRTEjm9LnOae'));
        $this->user->expects($this->once())
                   ->method('getId')
                   ->will($this->returnValue(1));

        $this->storage->expects($this->any())
                      ->method('getNameSpace')
                      ->will($this->returnValue('test'));

        $this->authEvent->expects($this->once())
                        ->method('setIdentity')
                        ->with(1)
                        ->will($this->returnValue($this->authEvent));
        $this->authEvent->expects($this->once())
                        ->method('setCode')
                        ->with(\Laminas\Authentication\Result::SUCCESS)
                        ->will($this->returnValue($this->authEvent));
        $this->authEvent->expects($this->once())
                        ->method('setMessages')
                        ->with(array('Authentication successful.'))
                        ->will($this->returnValue($this->authEvent));

        $this->db->setMapper($this->mapper);
        $this->db->setOptions($this->options);

        $event = new Event(null, $this->authEvent);
        $result = $this->db->authenticate($event);
    }

    /**
     * @covers \LaminasUser\Authentication\Adapter\Db::Authenticate
     */
    public function testAuthenticationAuthenticates()
    {
        $this->setAuthenticationCredentials();
        $this->setAuthenticationUser();

        $this->options->expects($this->once())
             ->method('getEnableUserState')
             ->will($this->returnValue(true));

        $this->options->expects($this->once())
             ->method('getAllowedLoginStates')
             ->will($this->returnValue(array(1, 2, 3)));

        $this->options->expects($this->once())
            ->method('getPasswordCost')
            ->will($this->returnValue(4));

        $this->user->expects($this->exactly(2))
                   ->method('getPassword')
                   ->will($this->returnValue('$2a$04$5kq1mnYWbww8X.rIj7eOVOHXtvGw/peefjIcm0lDGxRTEjm9LnOae'));
        $this->user->expects($this->once())
                   ->method('getId')
                   ->will($this->returnValue(1));
        $this->user->expects($this->once())
                   ->method('getState')
                   ->will($this->returnValue(1));

        $this->storage->expects($this->any())
                      ->method('getNameSpace')
                      ->will($this->returnValue('test'));

        $this->authEvent->expects($this->once())
                        ->method('setIdentity')
                        ->with(1)
                        ->will($this->returnValue($this->authEvent));
        $this->authEvent->expects($this->once())
                        ->method('setCode')
                        ->with(\Laminas\Authentication\Result::SUCCESS)
                        ->will($this->returnValue($this->authEvent));
        $this->authEvent->expects($this->once())
                        ->method('setMessages')
                        ->with(array('Authentication successful.'))
                        ->will($this->returnValue($this->authEvent));

        $this->db->setMapper($this->mapper);
        $this->db->setOptions($this->options);

        $event = new Event(null, $this->authEvent);
        $result = $this->db->authenticate($event);
    }

    /**
     * @covers \LaminasUser\Authentication\Adapter\Db::updateUserPasswordHash
     */
    public function testUpdateUserPasswordHashWithSameCost()
    {
        $user = $this->getMock('LaminasUser\Entity\User');
        $user->expects($this->once())
            ->method('getPassword')
            ->will($this->returnValue('$2a$10$x05G2P803MrB3jaORBXBn.QHtiYzGQOBjQ7unpEIge.Mrz6c3KiVm'));

        $bcrypt = $this->getMock('Laminas\Crypt\Password\Bcrypt');
        $bcrypt->expects($this->once())
            ->method('getCost')
            ->will($this->returnValue('10'));

        $method = new \ReflectionMethod(
            'LaminasUser\Authentication\Adapter\Db',
            'updateUserPasswordHash'
        );
        $method->setAccessible(true);

        $result = $method->invoke($this->db, $user, 'LaminasUser', $bcrypt);
        $this->assertNull($result);
    }

    /**
     * @covers \LaminasUser\Authentication\Adapter\Db::updateUserPasswordHash
     */
    public function testUpdateUserPasswordHashWithoutSameCost()
    {
        $user = $this->getMock('LaminasUser\Entity\User');
        $user->expects($this->once())
            ->method('getPassword')
            ->will($this->returnValue('$2a$10$x05G2P803MrB3jaORBXBn.QHtiYzGQOBjQ7unpEIge.Mrz6c3KiVm'));
        $user->expects($this->once())
            ->method('setPassword')
            ->with('$2a$10$D41KPuDCn6iGoESjnLee/uE/2Xo985sotVySo2HKDz6gAO4hO/Gh6');

        $bcrypt = $this->getMock('Laminas\Crypt\Password\Bcrypt');
        $bcrypt->expects($this->once())
            ->method('getCost')
            ->will($this->returnValue('5'));
        $bcrypt->expects($this->once())
            ->method('create')
            ->with('LaminasUserNew')
            ->will($this->returnValue('$2a$10$D41KPuDCn6iGoESjnLee/uE/2Xo985sotVySo2HKDz6gAO4hO/Gh6'));

        $mapper = $this->getMock('LaminasUser\Mapper\User');
        $mapper->expects($this->once())
            ->method('update')
            ->with($user);

        $this->db->setMapper($mapper);

        $method = new \ReflectionMethod(
            'LaminasUser\Authentication\Adapter\Db',
            'updateUserPasswordHash'
        );
        $method->setAccessible(true);

        $result = $method->invoke($this->db, $user, 'LaminasUserNew', $bcrypt);
        $this->assertInstanceOf('LaminasUser\Authentication\Adapter\Db', $result);
    }


    /**
     * @covers \LaminasUser\Authentication\Adapter\Db::preprocessCredential
     * @covers \LaminasUser\Authentication\Adapter\Db::setCredentialPreprocessor
     * @covers \LaminasUser\Authentication\Adapter\Db::getCredentialPreprocessor
     */
    public function testPreprocessCredentialWithCallable()
    {
        $test = $this;
        $testVar = false;
        $callable = function ($credential) use ($test, &$testVar) {
            $test->assertEquals('LaminasUser', $credential);
            $testVar = true;
        };
        $this->db->setCredentialPreprocessor($callable);

        $this->db->preProcessCredential('LaminasUser');
        $this->assertTrue($testVar);
    }

    /**
     * @covers \LaminasUser\Authentication\Adapter\Db::preprocessCredential
     * @covers \LaminasUser\Authentication\Adapter\Db::setCredentialPreprocessor
     * @covers \LaminasUser\Authentication\Adapter\Db::getCredentialPreprocessor
     */
    public function testPreprocessCredentialWithoutCallable()
    {
        $this->db->setCredentialPreprocessor(false);
        $this->assertSame('laminasUser', $this->db->preProcessCredential('laminasUser'));
    }

    /**
     * @covers \LaminasUser\Authentication\Adapter\Db::setServiceManager
     * @covers \LaminasUser\Authentication\Adapter\Db::getServiceManager
     */
    public function testSetGetServicemanager()
    {
        $sm = $this->getMock('Laminas\ServiceManager\ServiceManager');

        $this->db->setServiceManager($sm);

        $serviceManager = $this->db->getServiceManager();

        $this->assertInstanceOf('Laminas\ServiceManager\ServiceLocatorInterface', $serviceManager);
        $this->assertSame($sm, $serviceManager);
    }

    /**
     * @covers \LaminasUser\Authentication\Adapter\Db::getOptions
     */
    public function testGetOptionsWithNoOptionsSet()
    {
        $serviceMapper = $this->getMock('Laminas\ServiceManager\ServiceManager');
        $serviceMapper->expects($this->once())
            ->method('get')
            ->with('laminasuser_module_options')
            ->will($this->returnValue($this->options));

        $this->db->setServiceManager($serviceMapper);

        $options = $this->db->getOptions();

        $this->assertInstanceOf('LaminasUser\Options\ModuleOptions', $options);
        $this->assertSame($this->options, $options);
    }

    /**
     * @covers \LaminasUser\Authentication\Adapter\Db::setOptions
     * @covers \LaminasUser\Authentication\Adapter\Db::getOptions
     */
    public function testSetGetOptions()
    {
        $options = new \LaminasUser\Options\ModuleOptions;
        $options->setLoginRedirectRoute('laminasUser');

        $this->db->setOptions($options);

        $this->assertInstanceOf('LaminasUser\Options\ModuleOptions', $this->db->getOptions());
        $this->assertSame('laminasUser', $this->db->getOptions()->getLoginRedirectRoute());
    }

    /**
     * @covers \LaminasUser\Authentication\Adapter\Db::getMapper
     */
    public function testGetMapperWithNoMapperSet()
    {
        $serviceMapper = $this->getMock('Laminas\ServiceManager\ServiceManager');
        $serviceMapper->expects($this->once())
            ->method('get')
            ->with('laminasuser_user_mapper')
            ->will($this->returnValue($this->mapper));

        $this->db->setServiceManager($serviceMapper);

        $mapper = $this->db->getMapper();
        $this->assertInstanceOf('LaminasUser\Mapper\UserInterface', $mapper);
        $this->assertSame($this->mapper, $mapper);
    }

    /**
     * @covers \LaminasUser\Authentication\Adapter\Db::setMapper
     * @covers \LaminasUser\Authentication\Adapter\Db::getMapper
     */
    public function testSetGetMapper()
    {
        $mapper = new \LaminasUser\Mapper\User;
        $mapper->setTableName('laminasUser');

        $this->db->setMapper($mapper);

        $this->assertInstanceOf('LaminasUser\Mapper\User', $this->db->getMapper());
        $this->assertSame('laminasUser', $this->db->getMapper()->getTableName());
    }

    protected function setAuthenticationEmail()
    {
        $this->mapper->expects($this->once())
            ->method('findByEmail')
            ->with('laminas-user@zf-commons.io')
            ->will($this->returnValue($this->user));

        $this->options->expects($this->once())
            ->method('getAuthIdentityFields')
            ->will($this->returnValue(array('email')));
    }

    protected function setAuthenticationUser()
    {
        $this->mapper->expects($this->once())
            ->method('findByUsername')
            ->with('LaminasUser')
            ->will($this->returnValue($this->user));

        $this->options->expects($this->once())
            ->method('getAuthIdentityFields')
            ->will($this->returnValue(array('username')));
    }

    protected function setAuthenticationCredentials($identity = 'LaminasUser', $credential = 'LaminasUserPassword')
    {
        $this->storage->expects($this->at(0))
            ->method('read')
            ->will($this->returnValue(array('is_satisfied' => false)));

        $post = $this->getMock('Laminas\Stdlib\Parameters');
        $post->expects($this->at(0))
            ->method('get')
            ->with('identity')
            ->will($this->returnValue($identity));
        $post->expects($this->at(1))
            ->method('get')
            ->with('credential')
            ->will($this->returnValue($credential));

        $request = $this->getMock('Laminas\Http\Request');
        $request->expects($this->exactly(2))
            ->method('getPost')
            ->will($this->returnValue($post));

        $this->authEvent->expects($this->exactly(2))
            ->method('getRequest')
            ->will($this->returnValue($request));
    }
}
