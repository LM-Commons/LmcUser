<?php

namespace LmcUserTest\Form;

use LmcUser\Form\ChangePasswordFilter as Filter;
use PHPUnit\Framework\TestCase;

class ChangePasswordFilterTest extends TestCase
{
    public function testConstruct()
    {
        $options = $this->createMock('LmcUser\Options\ModuleOptions');
        $options->expects($this->once())
            ->method('getAuthIdentityFields')
            ->will($this->returnValue(array('email')));

        $filter = new Filter($options);

        $inputs = $filter->getInputs();
        $this->assertArrayHasKey('identity', $inputs);
        $this->assertArrayHasKey('credential', $inputs);
        $this->assertArrayHasKey('newCredential', $inputs);
        $this->assertArrayHasKey('newCredentialVerify', $inputs);

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
            ->will($this->returnValue($onlyEmail ? array('email') : array('username')));

        $filter = new Filter($options);

        $inputs = $filter->getInputs();
        $this->assertArrayHasKey('identity', $inputs);
        $this->assertArrayHasKey('credential', $inputs);
        $this->assertArrayHasKey('newCredential', $inputs);
        $this->assertArrayHasKey('newCredentialVerify', $inputs);

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

    public function providerTestConstructIdentityEmail()
    {
        return array(
            array(true),
            array(false)
        );
    }
}
