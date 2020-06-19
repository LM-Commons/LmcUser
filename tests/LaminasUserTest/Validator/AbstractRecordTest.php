<?php

namespace LaminasUserTest\Validator;

use LaminasUserTest\Validator\TestAsset\AbstractRecordExtension;

class AbstractRecordTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers LaminasUser\Validator\AbstractRecord::__construct
     */
    public function testConstruct()
    {
        $options = array('key'=>'value');
        new AbstractRecordExtension($options);
    }

    /**
     * @covers LaminasUser\Validator\AbstractRecord::__construct
     * @expectedException LaminasUser\Validator\Exception\InvalidArgumentException
     * @expectedExceptionMessage No key provided
     */
    public function testConstructEmptyArray()
    {
        $options = array();
        new AbstractRecordExtension($options);
    }

    /**
     * @covers LaminasUser\Validator\AbstractRecord::getMapper
     * @covers LaminasUser\Validator\AbstractRecord::setMapper
     */
    public function testGetSetMapper()
    {
        $options = array('key' => '');
        $validator = new AbstractRecordExtension($options);

        $this->assertNull($validator->getMapper());

        $mapper = $this->getMock('LaminasUser\Mapper\UserInterface');
        $validator->setMapper($mapper);
        $this->assertSame($mapper, $validator->getMapper());
    }

    /**
     * @covers LaminasUser\Validator\AbstractRecord::getKey
     * @covers LaminasUser\Validator\AbstractRecord::setKey
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
     * @covers LaminasUser\Validator\AbstractRecord::query
     * @expectedException \Exception
     * @expectedExceptionMessage Invalid key used in LaminasUser validator
     */
    public function testQueryWithInvalidKey()
    {
        $options = array('key' => 'zfcUser');
        $validator = new AbstractRecordExtension($options);

        $method = new \ReflectionMethod('LaminasUserTest\Validator\TestAsset\AbstractRecordExtension', 'query');
        $method->setAccessible(true);

        $method->invoke($validator, array('test'));
    }

    /**
     * @covers LaminasUser\Validator\AbstractRecord::query
     */
    public function testQueryWithKeyUsername()
    {
        $options = array('key' => 'username');
        $validator = new AbstractRecordExtension($options);

        $mapper = $this->getMock('LaminasUser\Mapper\UserInterface');
        $mapper->expects($this->once())
               ->method('findByUsername')
               ->with('test')
               ->will($this->returnValue('LaminasUser'));

        $validator->setMapper($mapper);

        $method = new \ReflectionMethod('LaminasUserTest\Validator\TestAsset\AbstractRecordExtension', 'query');
        $method->setAccessible(true);

        $result = $method->invoke($validator, 'test');

        $this->assertEquals('LaminasUser', $result);
    }

    /**
     * @covers LaminasUser\Validator\AbstractRecord::query
     */
    public function testQueryWithKeyEmail()
    {
        $options = array('key' => 'email');
        $validator = new AbstractRecordExtension($options);

        $mapper = $this->getMock('LaminasUser\Mapper\UserInterface');
        $mapper->expects($this->once())
            ->method('findByEmail')
            ->with('test@test.com')
            ->will($this->returnValue('LaminasUser'));

        $validator->setMapper($mapper);

        $method = new \ReflectionMethod('LaminasUserTest\Validator\TestAsset\AbstractRecordExtension', 'query');
        $method->setAccessible(true);

        $result = $method->invoke($validator, 'test@test.com');

        $this->assertEquals('LaminasUser', $result);
    }
}
