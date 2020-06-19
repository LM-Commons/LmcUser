<?php

namespace LaminasUserTest\Form;

use LaminasUser\Form\ChangePassword as Form;

class ChangePasswordTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers LaminasUser\Form\ChangePassword::__construct
     */
    public function testConstruct()
    {
        $options = $this->getMock('LaminasUser\Options\AuthenticationOptionsInterface');

        $form = new Form(null, $options);

        $elements = $form->getElements();

        $this->assertArrayHasKey('identity', $elements);
        $this->assertArrayHasKey('credential', $elements);
        $this->assertArrayHasKey('newCredential', $elements);
        $this->assertArrayHasKey('newCredentialVerify', $elements);
    }

    /**
     * @covers LaminasUser\Form\ChangePassword::getAuthenticationOptions
     * @covers LaminasUser\Form\ChangePassword::setAuthenticationOptions
     */
    public function testSetGetAuthenticationOptions()
    {
        $options = $this->getMock('LaminasUser\Options\AuthenticationOptionsInterface');
        $form = new Form(null, $options);

        $this->assertSame($options, $form->getAuthenticationOptions());
    }
}
