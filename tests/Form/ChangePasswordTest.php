<?php

namespace LmcUserTest\Form;

use LmcUser\Form\ChangePassword as Form;
use PHPUnit\Framework\TestCase;

class ChangePasswordTest extends TestCase
{
    /**
     * @covers LmcUser\Form\ChangePassword::__construct
     */
    public function testConstruct()
    {
        $options = $this->createMock('LmcUser\Options\AuthenticationOptionsInterface');

        $form = new Form(null, $options);

        $elements = $form->getElements();

        $this->assertArrayHasKey('identity', $elements);
        $this->assertArrayHasKey('credential', $elements);
        $this->assertArrayHasKey('newCredential', $elements);
        $this->assertArrayHasKey('newCredentialVerify', $elements);
    }

    /**
     * @covers LmcUser\Form\ChangePassword::getAuthenticationOptions
     * @covers LmcUser\Form\ChangePassword::setAuthenticationOptions
     */
    public function testSetGetAuthenticationOptions()
    {
        $options = $this->createMock('LmcUser\Options\AuthenticationOptionsInterface');
        $form = new Form(null, $options);

        $this->assertSame($options, $form->getAuthenticationOptions());
    }
}
