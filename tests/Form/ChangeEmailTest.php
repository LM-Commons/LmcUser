<?php

namespace LmcUserTest\Form;

use LmcUser\Form\ChangeEmail as Form;
use PHPUnit\Framework\TestCase;

class ChangeEmailTest extends TestCase
{
    /**
     * @covers LmcUser\Form\ChangeEmail::__construct
     */
    public function testConstruct()
    {
        $options = $this->createMock('LmcUser\Options\AuthenticationOptionsInterface');

        $form = new Form(null, $options);

        $elements = $form->getElements();

        $this->assertArrayHasKey('identity', $elements);
        $this->assertArrayHasKey('newIdentity', $elements);
        $this->assertArrayHasKey('newIdentityVerify', $elements);
        $this->assertArrayHasKey('credential', $elements);
    }

    /**
     * @covers LmcUser\Form\ChangeEmail::getAuthenticationOptions
     * @covers LmcUser\Form\ChangeEmail::setAuthenticationOptions
     */
    public function testSetGetAuthenticationOptions()
    {
        $options = $this->createMock('LmcUser\Options\AuthenticationOptionsInterface');
        $form = new Form(null, $options);

        $this->assertSame($options, $form->getAuthenticationOptions());
    }
}
