<?php

namespace LmcUserTest\Validator;

use LmcUser\Validator\NoRecordExists as Validator;

class NoRecordExistsTest extends \PHPUnit_Framework_TestCase
{
    protected $validator;

    protected $mapper;

    public function setUp()
    {
        $options = array('key' => 'username');
        $validator = new Validator($options);
        $this->validator = $validator;

        $mapper = $this->getMock('LmcUser\Mapper\UserInterface');
        $this->mapper = $mapper;

        $validator->setMapper($mapper);
    }

    /**
     * @covers LmcUser\Validator\NoRecordExists::isValid
     */
    public function testIsValid()
    {
        $this->mapper->expects($this->once())
                     ->method('findByUsername')
                     ->with('lmcUser')
                     ->will($this->returnValue(false));

        $result = $this->validator->isValid('lmcUser');
        $this->assertTrue($result);
    }

    /**
     * @covers LmcUser\Validator\NoRecordExists::isValid
     */
    public function testIsInvalid()
    {
        $this->mapper->expects($this->once())
                     ->method('findByUsername')
                     ->with('lmcUser')
                     ->will($this->returnValue('lmcUser'));

        $result = $this->validator->isValid('lmcUser');
        $this->assertFalse($result);

        $options = $this->validator->getOptions();
        $this->assertArrayHasKey(\LmcUser\Validator\AbstractRecord::ERROR_RECORD_FOUND, $options['messages']);
        $this->assertEquals($options['messageTemplates'][\LmcUser\Validator\AbstractRecord::ERROR_RECORD_FOUND], $options['messages'][\LmcUser\Validator\AbstractRecord::ERROR_RECORD_FOUND]);
    }
}
