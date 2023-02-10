<?php

namespace LmcUserTest\Form;

use LmcUser\Form\RegisterFilter as Filter;
use PHPUnit\Framework\TestCase;

class RegisterFilterTest extends TestCase
{
    /**
     * @covers LmcUser\Form\RegisterFilter::__construct
     */
    public function testConstruct()
    {
        $options = $this->createMock('LmcUser\Options\ModuleOptions');
        $options->expects($this->once())
            ->method('getEnableUsername')
            ->will($this->returnValue(true));
        $options->expects($this->once())
            ->method('getEnableDisplayName')
            ->will($this->returnValue(true));

        $emailValidator = $this->getMockBuilder('LmcUser\Validator\NoRecordExists')->disableOriginalConstructor()->getMock();
        $usernameValidator = $this->getMockBuilder('LmcUser\Validator\NoRecordExists')->disableOriginalConstructor()->getMock();

        $filter = new Filter($emailValidator, $usernameValidator, $options);

        $inputs = $filter->getInputs();
        $this->assertArrayHasKey('username', $inputs);
        $this->assertArrayHasKey('email', $inputs);
        $this->assertArrayHasKey('display_name', $inputs);
        $this->assertArrayHasKey('password', $inputs);
        $this->assertArrayHasKey('passwordVerify', $inputs);
    }

    public function testSetGetEmailValidator()
    {
        $options = $this->createMock('LmcUser\Options\ModuleOptions');
        $validatorInit = $this->getMockBuilder('LmcUser\Validator\NoRecordExists')->disableOriginalConstructor()->getMock();
        $validatorNew = $this->getMockBuilder('LmcUser\Validator\NoRecordExists')->disableOriginalConstructor()->getMock();

        $filter = new Filter($validatorInit, $validatorInit, $options);

        $this->assertSame($validatorInit, $filter->getEmailValidator());
        $filter->setEmailValidator($validatorNew);
        $this->assertSame($validatorNew, $filter->getEmailValidator());
    }

    public function testSetGetUsernameValidator()
    {
        $options = $this->createMock('LmcUser\Options\ModuleOptions');
        $validatorInit = $this->getMockBuilder('LmcUser\Validator\NoRecordExists')->disableOriginalConstructor()->getMock();
        $validatorNew = $this->getMockBuilder('LmcUser\Validator\NoRecordExists')->disableOriginalConstructor()->getMock();

        $filter = new Filter($validatorInit, $validatorInit, $options);

        $this->assertSame($validatorInit, $filter->getUsernameValidator());
        $filter->setUsernameValidator($validatorNew);
        $this->assertSame($validatorNew, $filter->getUsernameValidator());
    }

    public function testSetGetOptions()
    {
        $options = $this->createMock('LmcUser\Options\ModuleOptions');
        $optionsNew = $this->createMock('LmcUser\Options\ModuleOptions');
        $validatorInit = $this->getMockBuilder('LmcUser\Validator\NoRecordExists')->disableOriginalConstructor()->getMock();
        $filter = new Filter($validatorInit, $validatorInit, $options);

        $this->assertSame($options, $filter->getOptions());
        $filter->setOptions($optionsNew);
        $this->assertSame($optionsNew, $filter->getOptions());
    }
}
