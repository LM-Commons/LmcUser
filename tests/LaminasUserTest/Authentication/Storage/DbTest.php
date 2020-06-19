<?php

namespace LaminasUserTest\Authentication\Storage;

use LaminasUser\Authentication\Storage\Db;

class DbTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The object to be tested.
     *
     * @var Db
     */
    protected $db;

    /**
     * Mock of Storage.
     *
     * @var storage
     */
    protected $storage;

    /**
     * Mock of Mapper.
     *
     * @var mapper
     */
    protected $mapper;

    public function setUp()
    {
        $db = new Db;
        $this->db = $db;

        $this->storage = $this->getMock('Zend\Authentication\Storage\Session');
        $this->mapper = $this->getMock('LaminasUser\Mapper\User');
    }

    /**
     * @covers LaminasUser\Authentication\Storage\Db::isEmpty
     */
    public function testIsEmpty()
    {
        $this->storage->expects($this->once())
                      ->method('isEmpty')
                      ->will($this->returnValue(true));

        $this->db->setStorage($this->storage);

        $this->assertTrue($this->db->isEmpty());
    }

    /**
     * @covers LaminasUser\Authentication\Storage\Db::read
     */
    public function testReadWithResolvedEntitySet()
    {
        $reflectionClass = new \ReflectionClass('LaminasUser\Authentication\Storage\Db');
        $reflectionProperty = $reflectionClass->getProperty('resolvedIdentity');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this->db, 'zfcUser');

        $this->assertSame('zfcUser', $this->db->read());
    }

    /**
     * @covers LaminasUser\Authentication\Storage\Db::read
     */
    public function testReadWithoutResolvedEntitySetIdentityIntUserFound()
    {
        $this->storage->expects($this->once())
                      ->method('read')
                      ->will($this->returnValue(1));

        $this->db->setStorage($this->storage);

        $user = $this->getMock('LaminasUser\Entity\User');
        $user->setUsername('zfcUser');

        $this->mapper->expects($this->once())
                     ->method('findById')
                     ->with(1)
                     ->will($this->returnValue($user));

        $this->db->setMapper($this->mapper);

        $result = $this->db->read();

        $this->assertSame($user, $result);
    }

    /**
     * @covers LaminasUser\Authentication\Storage\Db::read
     */
    public function testReadWithoutResolvedEntitySetIdentityIntUserNotFound()
    {
        $this->storage->expects($this->once())
                      ->method('read')
                      ->will($this->returnValue(1));

        $this->db->setStorage($this->storage);

        $this->mapper->expects($this->once())
                     ->method('findById')
                     ->with(1)
                     ->will($this->returnValue(false));

        $this->db->setMapper($this->mapper);

        $result = $this->db->read();

        $this->assertNull($result);
    }

    /**
     * @covers LaminasUser\Authentication\Storage\Db::read
     */
    public function testReadWithoutResolvedEntitySetIdentityObject()
    {
        $user = $this->getMock('LaminasUser\Entity\User');
        $user->setUsername('zfcUser');

        $this->storage->expects($this->once())
                      ->method('read')
                      ->will($this->returnValue($user));

        $this->db->setStorage($this->storage);

        $result = $this->db->read();

        $this->assertSame($user, $result);
    }

    /**
     * @covers LaminasUser\Authentication\Storage\Db::write
     */
    public function testWrite()
    {
        $reflectionClass = new \ReflectionClass('LaminasUser\Authentication\Storage\Db');
        $reflectionProperty = $reflectionClass->getProperty('resolvedIdentity');
        $reflectionProperty->setAccessible(true);

        $this->storage->expects($this->once())
                      ->method('write')
                      ->with('zfcUser');

        $this->db->setStorage($this->storage);

        $this->db->write('zfcUser');

        $this->assertNull($reflectionProperty->getValue($this->db));
    }

    /**
     * @covers LaminasUser\Authentication\Storage\Db::clear
     */
    public function testClear()
    {
        $reflectionClass = new \ReflectionClass('LaminasUser\Authentication\Storage\Db');
        $reflectionProperty = $reflectionClass->getProperty('resolvedIdentity');
        $reflectionProperty->setAccessible(true);

        $this->storage->expects($this->once())
            ->method('clear');

        $this->db->setStorage($this->storage);

        $this->db->clear();

        $this->assertNull($reflectionProperty->getValue($this->db));
    }

    /**
     * @covers LaminasUser\Authentication\Storage\Db::getMapper
     */
    public function testGetMapperWithNoMapperSet()
    {
        $sm = $this->getMock('Zend\ServiceManager\ServiceManager');
        $sm->expects($this->once())
           ->method('get')
           ->with('zfcuser_user_mapper')
           ->will($this->returnValue($this->mapper));

        $this->db->setServiceManager($sm);

        $this->assertInstanceOf('LaminasUser\Mapper\UserInterface', $this->db->getMapper());
    }

    /**
     * @covers LaminasUser\Authentication\Storage\Db::setMapper
     * @covers LaminasUser\Authentication\Storage\Db::getMapper
     */
    public function testSetGetMapper()
    {
        $mapper = new \LaminasUser\Mapper\User;
        $mapper->setTableName('zfcUser');

        $this->db->setMapper($mapper);

        $this->assertInstanceOf('LaminasUser\Mapper\User', $this->db->getMapper());
        $this->assertSame('zfcUser', $this->db->getMapper()->getTableName());
    }

    /**
     * @covers LaminasUser\Authentication\Storage\Db::setServiceManager
     * @covers LaminasUser\Authentication\Storage\Db::getServiceManager
     */
    public function testSetGetServicemanager()
    {
        $sm = $this->getMock('Zend\ServiceManager\ServiceManager');

        $this->db->setServiceManager($sm);

        $this->assertInstanceOf('Zend\ServiceManager\ServiceLocatorInterface', $this->db->getServiceManager());
        $this->assertSame($sm, $this->db->getServiceManager());
    }

    /**
     * @covers LaminasUser\Authentication\Storage\Db::getStorage
     * @covers LaminasUser\Authentication\Storage\Db::setStorage
     */
    public function testGetStorageWithoutStorageSet()
    {
        $this->assertInstanceOf('Zend\Authentication\Storage\Session', $this->db->getStorage());
    }

    /**
     * @covers LaminasUser\Authentication\Storage\Db::getStorage
     * @covers LaminasUser\Authentication\Storage\Db::setStorage
     */
    public function testSetGetStorage()
    {
        $storage = new \Zend\Authentication\Storage\Session('LaminasUserStorage');
        $this->db->setStorage($storage);

        $this->assertInstanceOf('Zend\Authentication\Storage\Session', $this->db->getStorage());
    }
}
