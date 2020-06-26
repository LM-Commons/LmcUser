<?php

namespace LmcUserTest\Validator;

use LmcUserTest\Validator\TestAsset\AbstractRecordExtension;

class AbstractRecordTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers LmcUser\Validator\AbstractRecord::__construct
     */
    public function testConstruct()
    {
        $options = array('key'=>'value');
        new AbstractRecordExtension($options);
    }

    /**
     * @covers LmcUser\Validator\AbstractRecord::__construct
     * @expectedException LmcUser\Validator\Exception\InvalidArgumentException
     * @expectedExceptionMessage No key provided
     */
    public function testConstructEmptyArray()
    {
        $options = array();
        new AbstractRecordExtension($options);
    }

    /**
     * @covers LmcUser\Validator\AbstractRecord::getMapper
     * @covers LmcUser\Validator\AbstractRecord::setMapper
     */
    public function testGetSetMapper()
    {
        $options = array('key' => '');
        $validator = new AbstractRecordExtension($options);

        $this->assertNull($validator->getMapper());

        $mapper = $this->getMock('LmcUser\Mapper\UserInterface');
        $validator->setMapper($mapper);
        $this->assertSame($mapper, $validator->getMapper());
    }

    /**
     * @covers LmcUser\Validator\AbstractRecord::getKey
     * @covers LmcUser\Validator\AbstractRecord::setKey
     */
    public function testGetSetKey()
    {
        $options = array('key' => 'username');
        $validator = new AbstractRecordExtension($options);

        $this->assertEquals('username', $validator->getKey());

        $validator->setKey('email');
        $this->assertEquals('email', $validator->getKey());
    }

    /**
     * @covers LmcUser\Validator\AbstractRecord::query
     * @expectedException \Exception
     * @expectedExceptionMessage Invalid key used in LmcUser validator
     */
    public function testQueryWithInvalidKey()
    {
        $options = array('key' => 'lmcUser');
        $validator = new AbstractRecordExtension($options);

        $method = new \ReflectionMethod('LmcUserTest\Validator\TestAsset\AbstractRecordExtension', 'query');
        $method->setAccessible(true);

        $method->invoke($validator, array('test'));
    }

    /**
     * @covers LmcUser\Validator\AbstractRecord::query
     */
    public function testQueryWithKeyUsername()
    {
        $options = array('key' => 'username');
        $validator = new AbstractRecordExtension($options);

        $mapper = $this->getMock('LmcUser\Mapper\UserInterface');
        $mapper->expects($this->once())
               ->method('findByUsername')
               ->with('test')
               ->will($this->returnValue('LmcUser'));

        $validator->setMapper($mapper);

        $method = new \ReflectionMethod('LmcUserTest\Validator\TestAsset\AbstractRecordExtension', 'query');
        $method->setAccessible(true);

        $result = $method->invoke($validator, 'test');

        $this->assertEquals('LmcUser', $result);
    }

    /**
     * @covers LmcUser\Validator\AbstractRecord::query
     */
    public function testQueryWithKeyEmail()
    {
        $options = array('key' => 'email');
        $validator = new AbstractRecordExtension($options);

        $mapper = $this->getMock('LmcUser\Mapper\UserInterface');
        $mapper->expects($this->once())
            ->method('findByEmail')
            ->with('test@test.com')
            ->will($this->returnValue('LmcUser'));

        $validator->setMapper($mapper);

        $method = new \ReflectionMethod('LmcUserTest\Validator\TestAsset\AbstractRecordExtension', 'query');
        $method->setAccessible(true);

        $result = $method->invoke($validator, 'test@test.com');

        $this->assertEquals('LmcUser', $result);
    }
}
