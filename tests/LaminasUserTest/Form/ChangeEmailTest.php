<?php

namespace LaminasUserTest\Form;

use LaminasUser\Form\ChangeEmail as Form;

class ChangeEmailTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers LaminasUser\Form\ChangeEmail::__construct
     */
    public function testConstruct()
    {
        $options = $this->getMock('LaminasUser\Options\AuthenticationOptionsInterface');

        $form = new Form(null, $options);

        $elements = $form->getElements();

        $this->assertArrayHasKey('identity', $elements);
        $this->assertArrayHasKey('newIdentity', $elements);
        $this->assertArrayHasKey('newIdentityVerify', $elements);
        $this->assertArrayHasKey('credential', $elements);
    }

    /**
     * @covers LaminasUser\Form\ChangeEmail::getAuthenticationOptions
     * @covers LaminasUser\Form\ChangeEmail::setAuthenticationOptions
     */
    public function testSetGetAuthenticationOptions()
    {
        $options = $this->getMock('LaminasUser\Options\AuthenticationOptionsInterface');
        $form = new Form(null, $options);

        $this->assertSame($options, $form->getAuthenticationOptions());
    }
}
