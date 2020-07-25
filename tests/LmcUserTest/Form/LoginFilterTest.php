<?php

namespace LmcUserTest\Form;

use LmcUser\Form\LoginFilter as Filter;
use PHPUnit\Framework\TestCase;

class LoginFilterTest extends TestCase
{
    /**
     * @covers LmcUser\Form\LoginFilter::__construct
     */
    public function testConstruct()
    {
        $options = $this->createMock('LmcUser\Options\ModuleOptions');
        $options->expects($this->once())
            ->method('getAuthIdentityFields')
            ->will($this->returnValue(array()));

        $filter = new Filter($options);

        $inputs = $filter->getInputs();
        $this->assertArrayHasKey('identity', $inputs);
        $this->assertArrayHasKey('credential', $inputs);

        $this->assertEquals(0, $inputs['identity']->getValidatorChain()->count());
    }

    /**
     * @covers LmcUser\Form\LoginFilter::__construct
     */
    public function testConstructIdentityEmail()
    {
        $options = $this->createMock('LmcUser\Options\ModuleOptions');
        $options->expects($this->once())
            ->method('getAuthIdentityFields')
            ->will($this->returnValue(array('email')));

        $filter = new Filter($options);

        $inputs = $filter->getInputs();
        $this->assertArrayHasKey('identity', $inputs);
        $this->assertArrayHasKey('credential', $inputs);

        $identity = $inputs['identity'];

        // test email as identity
        $validators = $identity->getValidatorChain()->getValidators();
        $this->assertArrayHasKey('instance', $validators[0]);
        $this->assertInstanceOf('\Laminas\Validator\EmailAddress', $validators[0]['instance']);
    }
}
