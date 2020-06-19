<?php

namespace LaminasUserTest\Form;

use LaminasUser\Form\RegisterFilter as Filter;

class RegisterFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers LaminasUser\Form\RegisterFilter::__construct
     */
    public function testConstruct()
    {
        $options = $this->getMock('LaminasUser\Options\ModuleOptions');
        $options->expects($this->once())
                ->method('getEnableUsername')
                ->will($this->returnValue(true));
        $options->expects($this->once())
                ->method('getEnableDisplayName')
                ->will($this->returnValue(true));

        $emailValidator = $this->getMockBuilder('LaminasUser\Validator\NoRecordExists')->disableOriginalConstructor()->getMock();
        $usernameValidator = $this->getMockBuilder('LaminasUser\Validator\NoRecordExists')->disableOriginalConstructor()->getMock();

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
        $options = $this->getMock('LaminasUser\Options\ModuleOptions');
        $validatorInit = $this->getMockBuilder('LaminasUser\Validator\NoRecordExists')->disableOriginalConstructor()->getMock();
        $validatorNew = $this->getMockBuilder('LaminasUser\Validator\NoRecordExists')->disableOriginalConstructor()->getMock();

        $filter = new Filter($validatorInit, $validatorInit, $options);

        $this->assertSame($validatorInit, $filter->getEmailValidator());
        $filter->setEmailValidator($validatorNew);
        $this->assertSame($validatorNew, $filter->getEmailValidator());
    }

    public function testSetGetUsernameValidator()
    {
        $options = $this->getMock('LaminasUser\Options\ModuleOptions');
        $validatorInit = $this->getMockBuilder('LaminasUser\Validator\NoRecordExists')->disableOriginalConstructor()->getMock();
        $validatorNew = $this->getMockBuilder('LaminasUser\Validator\NoRecordExists')->disableOriginalConstructor()->getMock();

        $filter = new Filter($validatorInit, $validatorInit, $options);

        $this->assertSame($validatorInit, $filter->getUsernameValidator());
        $filter->setUsernameValidator($validatorNew);
        $this->assertSame($validatorNew, $filter->getUsernameValidator());
    }

    public function testSetGetOptions()
    {
        $options = $this->getMock('LaminasUser\Options\ModuleOptions');
        $optionsNew = $this->getMock('LaminasUser\Options\ModuleOptions');
        $validatorInit = $this->getMockBuilder('LaminasUser\Validator\NoRecordExists')->disableOriginalConstructor()->getMock();
        $filter = new Filter($validatorInit, $validatorInit, $options);

        $this->assertSame($options, $filter->getOptions());
        $filter->setOptions($optionsNew);
        $this->assertSame($optionsNew, $filter->getOptions());
    }
}
