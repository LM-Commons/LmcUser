<?php

namespace LmcUserTest\Authentication\Adapter;

use Laminas\EventManager\Event;
use LmcUser\Authentication\Adapter\Db;
use PHPUnit\Framework\TestCase;

class DbTest extends TestCase
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
     * @var \LmcUser\Authentication\Adapter\AdapterChainEvent|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $authEvent;

    /**
     * Mock of Storage.
     *
     * @var \Laminas\Authentication\Storage\Session|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $storage;

    /**
     * Mock of Options.
     *
     * @var \LmcUser\Options\ModuleOptions|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $options;

    /**
     * Mock of Mapper.
     *
     * @var \LmcUser\Mapper\UserInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $mapper;

    /**
     * Mock of User.
     *
     * @var \LmcUser\Entity\UserInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $user;

    protected function setUp():void
    {
        $storage = $this->createMock('Laminas\Authentication\Storage\Session');
        $this->storage = $storage;

        $authEvent = $this->createMock('LmcUser\Authentication\Adapter\AdapterChainEvent');
        $this->authEvent = $authEvent;

        $options = $this->createMock('LmcUser\Options\ModuleOptions');
        $this->options = $options;

        $mapper = $this->createMock('LmcUser\Mapper\UserInterface');
        $this->mapper = $mapper;

        $user = $this->createMock('LmcUser\Entity\UserInterface');
        $this->user = $user;

        $this->db = new Db;
        $this->db->setStorage($this->storage);

        $sessionManager = $this->createMock('Laminas\Session\SessionManager');
        \Laminas\Session\AbstractContainer::setDefaultManager($sessionManager);
    }

    /**
     * @covers \LmcUser\Authentication\Adapter\Db::logout
     */
    public function testLogout()
    {
        $this->storage->expects($this->once())
            ->method('clear');

         $this->db->logout($this->authEvent);
    }

    /**
     * @covers \LmcUser\Authentication\Adapter\Db::Authenticate
     */
    public function testAuthenticateWhenSatisfies()
    {
        $this->authEvent->expects($this->once())
            ->method('setIdentity')
            ->with('LmcUser')
            ->willReturn($this->authEvent);
        $this->authEvent->expects($this->once())
            ->method('setCode')
            ->with(\Laminas\Authentication\Result::SUCCESS)
            ->willReturn($this->authEvent);
        $this->authEvent->expects($this->once())
            ->method('setMessages')
            ->with(array('Authentication successful.'))
            ->willReturn($this->authEvent);

        $this->storage->expects($this->exactly(2))
            ->method('read')
            ->willReturnOnConsecutiveCalls(
                ['is_satisfied' => true],
                ['identity' => 'LmcUser']
            );


        $result = $this->db->authenticate($this->authEvent);
        $this->assertNull($result);
    }

    /**
     * @covers \LmcUser\Authentication\Adapter\Db::Authenticate
     */
    public function testAuthenticateNoUserObject()
    {
        $this->setAuthenticationCredentials();

        $this->options->expects($this->once())
            ->method('getAuthIdentityFields')
            ->willReturn(array());

        $this->authEvent->expects($this->once())
            ->method('setCode')
            ->with(\Laminas\Authentication\Result::FAILURE_IDENTITY_NOT_FOUND)
            ->willReturn($this->authEvent);
        $this->authEvent->expects($this->once())
            ->method('setMessages')
            ->with(array('A record with the supplied identity could not be found.'))
            ->willReturn($this->authEvent);

        $this->db->setOptions($this->options);

        
        $result = $this->db->authenticate($this->authEvent);

        $this->assertFalse($result);
        $this->assertFalse($this->db->isSatisfied());
    }

    /**
     * @covers \LmcUser\Authentication\Adapter\Db::Authenticate
     */
    public function testAuthenticationUserStateEnabledUserButUserStateNotInArray()
    {
        $this->setAuthenticationCredentials();
        $this->setAuthenticationUser();

        $this->options->expects($this->once())
            ->method('getEnableUserState')
            ->willReturn(true);
        $this->options->expects($this->once())
            ->method('getAllowedLoginStates')
            ->willReturn(array(2, 3));

        $this->authEvent->expects($this->once())
            ->method('setCode')
            ->with(\Laminas\Authentication\Result::FAILURE_UNCATEGORIZED)
            ->willReturn($this->authEvent);
        $this->authEvent->expects($this->once())
            ->method('setMessages')
            ->with(array('A record with the supplied identity is not active.'))
            ->willReturn($this->authEvent);

        $this->user->expects($this->once())
            ->method('getState')
            ->willReturn(1);

        $this->db->setMapper($this->mapper);
        $this->db->setOptions($this->options);

        
        $result = $this->db->authenticate($this->authEvent);

        $this->assertFalse($result);
        $this->assertFalse($this->db->isSatisfied());
    }

    /**
     * @covers \LmcUser\Authentication\Adapter\Db::Authenticate
     */
    public function testAuthenticateWithWrongPassword()
    {
        $this->setAuthenticationCredentials();
        $this->setAuthenticationUser();

        $this->options->expects($this->once())
            ->method('getEnableUserState')
            ->willReturn(false);

        // Set lowest possible to spent the least amount of resources/time
        $this->options->expects($this->once())
            ->method('getPasswordCost')
            ->willReturn(4);

        $this->authEvent->expects($this->once())
            ->method('setCode')
            ->with(\Laminas\Authentication\Result::FAILURE_CREDENTIAL_INVALID)
            ->willReturn($this->authEvent);
        $this->authEvent->expects($this->once(1))
            ->method('setMessages')
            ->with(array('Supplied credential is invalid.'));

        $this->db->setMapper($this->mapper);
        $this->db->setOptions($this->options);

        
        $result = $this->db->authenticate($this->authEvent);

        $this->assertFalse($result);
        $this->assertFalse($this->db->isSatisfied());
    }

    /**
     * @covers \LmcUser\Authentication\Adapter\Db::Authenticate
     */
    public function testAuthenticationAuthenticatesWithEmail()
    {
        $this->setAuthenticationCredentials('lmc-user@zf-commons.io');
        $this->setAuthenticationEmail();

        $this->options->expects($this->once())
            ->method('getEnableUserState')
            ->willReturn(false);

        $this->options->expects($this->once())
            ->method('getPasswordCost')
            ->willReturn(4);

        $this->user->expects($this->exactly(2))
            ->method('getPassword')
            ->willReturn('$2y$04$QVAIS1VWJZt6vQkWoWSHMet9ebjdKuKQGcjAEaILVQZjreRw0EAV2');
        $this->user->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $this->storage->expects($this->any())
            ->method('getNameSpace')
            ->willReturn('test');

        $this->authEvent->expects($this->once())
            ->method('setIdentity')
            ->with(1)
            ->willReturn($this->authEvent);
        $this->authEvent->expects($this->once())
            ->method('setCode')
            ->with(\Laminas\Authentication\Result::SUCCESS)
            ->willReturn($this->authEvent);
        $this->authEvent->expects($this->once())
            ->method('setMessages')
            ->with(array('Authentication successful.'))
            ->willReturn($this->authEvent);

        $this->db->setMapper($this->mapper);
        $this->db->setOptions($this->options);

        
        $result = $this->db->authenticate($this->authEvent);
    }

    /**
     * @covers \LmcUser\Authentication\Adapter\Db::Authenticate
     */
    public function testAuthenticationAuthenticates()
    {
        $this->setAuthenticationCredentials();
        $this->setAuthenticationUser();

        $this->options->expects($this->once())
            ->method('getEnableUserState')
            ->willReturn(true);

        $this->options->expects($this->once())
            ->method('getAllowedLoginStates')
            ->willReturn(array(1, 2, 3));

        $this->options->expects($this->once())
            ->method('getPasswordCost')
            ->willReturn(4);

        $this->user->expects($this->exactly(2))
            ->method('getPassword')
            ->willReturn('$2y$04$QVAIS1VWJZt6vQkWoWSHMet9ebjdKuKQGcjAEaILVQZjreRw0EAV2');
        $this->user->expects($this->once())
            ->method('getId')
            ->willReturn(1);
        $this->user->expects($this->once())
            ->method('getState')
            ->willReturn(1);

        $this->storage->expects($this->any())
            ->method('getNameSpace')
            ->willReturn('test');

        $this->authEvent->expects($this->once())
            ->method('setIdentity')
            ->with(1)
            ->willReturn($this->authEvent);
        $this->authEvent->expects($this->once())
            ->method('setCode')
            ->with(\Laminas\Authentication\Result::SUCCESS)
            ->willReturn($this->authEvent);
        $this->authEvent->expects($this->once())
            ->method('setMessages')
            ->with(array('Authentication successful.'))
            ->willReturn($this->authEvent);

        $this->db->setMapper($this->mapper);
        $this->db->setOptions($this->options);

        
        $result = $this->db->authenticate($this->authEvent);
    }

    /**
     * @covers \LmcUser\Authentication\Adapter\Db::updateUserPasswordHash
     */
    public function testUpdateUserPasswordHashWithSameCost()
    {
        $user = $this->createMock('LmcUser\Entity\User');
        $user->expects($this->once())
            ->method('getPassword')
            ->willReturn('$2a$10$x05G2P803MrB3jaORBXBn.QHtiYzGQOBjQ7unpEIge.Mrz6c3KiVm');

        $bcrypt = $this->createMock('Laminas\Crypt\Password\Bcrypt');
        $bcrypt->expects($this->once())
            ->method('getCost')
            ->willReturn('10');

        $method = new \ReflectionMethod(
            'LmcUser\Authentication\Adapter\Db',
            'updateUserPasswordHash'
        );
        $method->setAccessible(true);

        $result = $method->invoke($this->db, $user, 'LmcUser', $bcrypt);
        $this->assertNull($result);
    }

    /**
     * @covers \LmcUser\Authentication\Adapter\Db::updateUserPasswordHash
     */
    public function testUpdateUserPasswordHashWithoutSameCost()
    {
        $user = $this->createMock('LmcUser\Entity\User');
        $user->expects($this->once())
            ->method('getPassword')
            ->willReturn('$2a$10$x05G2P803MrB3jaORBXBn.QHtiYzGQOBjQ7unpEIge.Mrz6c3KiVm');
        $user->expects($this->once())
            ->method('setPassword')
            ->with('$2a$10$D41KPuDCn6iGoESjnLee/uE/2Xo985sotVySo2HKDz6gAO4hO/Gh6');

        $bcrypt = $this->createMock('Laminas\Crypt\Password\Bcrypt');
        $bcrypt->expects($this->once())
            ->method('getCost')
            ->willReturn('5');
        $bcrypt->expects($this->once())
            ->method('create')
            ->with('LmcUserNew')
            ->willReturn('$2a$10$D41KPuDCn6iGoESjnLee/uE/2Xo985sotVySo2HKDz6gAO4hO/Gh6');

        $mapper = $this->createMock('LmcUser\Mapper\User');
        $mapper->expects($this->once())
            ->method('update')
            ->with($user);

        $this->db->setMapper($mapper);

        $method = new \ReflectionMethod(
            'LmcUser\Authentication\Adapter\Db',
            'updateUserPasswordHash'
        );
        $method->setAccessible(true);

        $result = $method->invoke($this->db, $user, 'LmcUserNew', $bcrypt);
        $this->assertInstanceOf('LmcUser\Authentication\Adapter\Db', $result);
    }


    /**
     * @covers \LmcUser\Authentication\Adapter\Db::preprocessCredential
     * @covers \LmcUser\Authentication\Adapter\Db::setCredentialPreprocessor
     * @covers \LmcUser\Authentication\Adapter\Db::getCredentialPreprocessor
     */
    public function testPreprocessCredentialWithCallable()
    {
        $test = $this;
        $testVar = false;
        $callable = function ($credential) use ($test, &$testVar) {
            $test->assertEquals('LmcUser', $credential);
            $testVar = true;
        };
        $this->db->setCredentialPreprocessor($callable);

        $this->db->preProcessCredential('LmcUser');
        $this->assertTrue($testVar);
    }

    /**
     * @covers \LmcUser\Authentication\Adapter\Db::preprocessCredential
     * @covers \LmcUser\Authentication\Adapter\Db::setCredentialPreprocessor
     * @covers \LmcUser\Authentication\Adapter\Db::getCredentialPreprocessor
     */
    public function testPreprocessCredentialWithoutCallable()
    {
        $this->db->setCredentialPreprocessor(false);
        $this->assertSame('lmcUser', $this->db->preProcessCredential('lmcUser'));
    }

    /**
     * @covers \LmcUser\Authentication\Adapter\Db::setServiceManager
     * @covers \LmcUser\Authentication\Adapter\Db::getServiceManager
     */
    public function testSetGetServicemanager()
    {
        $sm = $this->createMock('Laminas\ServiceManager\ServiceManager');

        $this->db->setServiceManager($sm);

        $serviceManager = $this->db->getServiceManager();

        $this->assertInstanceOf('Laminas\ServiceManager\ServiceLocatorInterface', $serviceManager);
        $this->assertSame($sm, $serviceManager);
    }

    /**
     * @covers \LmcUser\Authentication\Adapter\Db::getOptions
     */
    public function testGetOptionsWithNoOptionsSet()
    {
        $serviceMapper = $this->createMock('Laminas\ServiceManager\ServiceManager');
        $serviceMapper->expects($this->once())
            ->method('get')
            ->with('lmcuser_module_options')
            ->willReturn($this->options);

        $this->db->setServiceManager($serviceMapper);

        $options = $this->db->getOptions();

        $this->assertInstanceOf('LmcUser\Options\ModuleOptions', $options);
        $this->assertSame($this->options, $options);
    }

    /**
     * @covers \LmcUser\Authentication\Adapter\Db::setOptions
     * @covers \LmcUser\Authentication\Adapter\Db::getOptions
     */
    public function testSetGetOptions()
    {
        $options = new \LmcUser\Options\ModuleOptions;
        $options->setLoginRedirectRoute('lmcUser');

        $this->db->setOptions($options);

        $this->assertInstanceOf('LmcUser\Options\ModuleOptions', $this->db->getOptions());
        $this->assertSame('lmcUser', $this->db->getOptions()->getLoginRedirectRoute());
    }

    /**
     * @covers \LmcUser\Authentication\Adapter\Db::getMapper
     */
    public function testGetMapperWithNoMapperSet()
    {
        $serviceMapper = $this->createMock('Laminas\ServiceManager\ServiceManager');
        $serviceMapper->expects($this->once())
            ->method('get')
            ->with('lmcuser_user_mapper')
            ->willReturn($this->mapper);

        $this->db->setServiceManager($serviceMapper);

        $mapper = $this->db->getMapper();
        $this->assertInstanceOf('LmcUser\Mapper\UserInterface', $mapper);
        $this->assertSame($this->mapper, $mapper);
    }

    /**
     * @covers \LmcUser\Authentication\Adapter\Db::setMapper
     * @covers \LmcUser\Authentication\Adapter\Db::getMapper
     */
    public function testSetGetMapper()
    {
        $mapper = new \LmcUser\Mapper\User;
        $mapper->setTableName('lmcUser');

        $this->db->setMapper($mapper);

        $this->assertInstanceOf('LmcUser\Mapper\User', $this->db->getMapper());
        $this->assertSame('lmcUser', $this->db->getMapper()->getTableName());
    }

    protected function setAuthenticationEmail()
    {
        $this->mapper->expects($this->once())
            ->method('findByEmail')
            ->with('lmc-user@zf-commons.io')
            ->willReturn($this->user);

        $this->options->expects($this->once())
            ->method('getAuthIdentityFields')
            ->willReturn(array('email'));
    }

    protected function setAuthenticationUser()
    {
        $this->mapper->expects($this->once())
            ->method('findByUsername')
            ->with('LmcUser')
            ->willReturn($this->user);

        $this->options->expects($this->once())
            ->method('getAuthIdentityFields')
            ->willReturn(array('username'));
    }

    protected function setAuthenticationCredentials($identity = 'LmcUser', $credential = 'LmcUserPassword')
    {
        $this->storage->expects($this->atLeastOnce())
            ->method('read')
            ->willReturn(['is_satisfied' => false]);

        $post = $this->createMock('Laminas\Stdlib\Parameters');
        $post->expects($this->atLeastOnce())
            ->method('get')
            ->withConsecutive(['identity'], ['credential'])
            ->willReturnOnConsecutiveCalls($identity, $credential);

        $request = $this->createMock('Laminas\Http\Request');
        $request->expects($this->exactly(2))
            ->method('getPost')
            ->willReturn($post);

        $this->authEvent->expects($this->exactly(2))
            ->method('getRequest')
            ->willReturn($request);
    }
}
