<?php

namespace LmcUserTest\Validator;

use LmcUser\Validator\Exception\InvalidArgumentException;
use LmcUserTest\Validator\TestAsset\AbstractRecordExtension;
use PHPUnit\Framework\TestCase;

class AbstractRecordTest extends TestCase
{
    /**
     * @covers LmcUser\Validator\AbstractRecord::__construct
     */
    public function testConstruct()
    {
        $options = array('key'=>'value');
        $this->assertIsObject(new AbstractRecordExtension($options));
    }

    /**
     * @covers LmcUser\Validator\AbstractRecord::__construct
     */
    public function testConstructEmptyArray()
    {
        $this->expectExceptionMessage("No key provided");
        $this->expectException(InvalidArgumentException::class);
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

        $mapper = $this->createMock('LmcUser\Mapper\UserInterface');
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
     */
    public function testQueryWithInvalidKey()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Invalid key used in LmcUser validator");
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

        $mapper = $this->createMock('LmcUser\Mapper\UserInterface');
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

        $mapper = $this->createMock('LmcUser\Mapper\UserInterface');
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
