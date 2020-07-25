<?php

namespace LmcUserTest\Form;

use LmcUser\Form\ChangeEmailFilter as Filter;
use PHPUnit\Framework\TestCase;

class ChangeEmailFilterTest extends TestCase
{
    public function testConstruct()
    {
        $options = $this->createMock('LmcUser\Options\ModuleOptions');
        $options->expects($this->once())
            ->method('getAuthIdentityFields')
            ->will($this->returnValue(array('email')));

        $validator = $this->getMockBuilder('LmcUser\Validator\NoRecordExists')->disableOriginalConstructor()->getMock();
        $filter = new Filter($options, $validator);

        $inputs = $filter->getInputs();
        $this->assertArrayHasKey('identity', $inputs);
        $this->assertArrayHasKey('newIdentity', $inputs);
        $this->assertArrayHasKey('newIdentityVerify', $inputs);

        $validators = $inputs['identity']->getValidatorChain()->getValidators();
        $this->assertArrayHasKey('instance', $validators[0]);
        $this->assertInstanceOf('\Laminas\Validator\EmailAddress', $validators[0]['instance']);
    }

    /**
     * @dataProvider providerTestConstructIdentityEmail
     */
    public function testConstructIdentityEmail($onlyEmail)
    {
        $options = $this->createMock('LmcUser\Options\ModuleOptions');
        $options->expects($this->once())
            ->method('getAuthIdentityFields')
            ->will($this->returnValue(($onlyEmail) ? array('email') : array('username')));

        $validator = $this->getMockBuilder('LmcUser\Validator\NoRecordExists')->disableOriginalConstructor()->getMock();
        $filter = new Filter($options, $validator);

        $inputs = $filter->getInputs();
        $this->assertArrayHasKey('identity', $inputs);
        $this->assertArrayHasKey('newIdentity', $inputs);
        $this->assertArrayHasKey('newIdentityVerify', $inputs);

        $identity = $inputs['identity'];

        if ($onlyEmail === false) {
            $this->assertEquals(0, $inputs['identity']->getValidatorChain()->count());
        } else {
            // test email as identity
            $validators = $identity->getValidatorChain()->getValidators();
            $this->assertArrayHasKey('instance', $validators[0]);
            $this->assertInstanceOf('\Laminas\Validator\EmailAddress', $validators[0]['instance']);
        }
    }

    public function testSetGetEmailValidator()
    {
        $options = $this->createMock('LmcUser\Options\ModuleOptions');
        $options->expects($this->once())
            ->method('getAuthIdentityFields')
            ->will($this->returnValue(array()));

        $validatorInit = $this->getMockBuilder('LmcUser\Validator\NoRecordExists')->disableOriginalConstructor()->getMock();
        $validatorNew = $this->getMockBuilder('LmcUser\Validator\NoRecordExists')->disableOriginalConstructor()->getMock();

        $filter = new Filter($options, $validatorInit);

        $this->assertSame($validatorInit, $filter->getEmailValidator());
        $filter->setEmailValidator($validatorNew);
        $this->assertSame($validatorNew, $filter->getEmailValidator());
    }

    public function providerTestConstructIdentityEmail()
    {
        return array(
            array(true),
            array(false)
        );
    }
}
