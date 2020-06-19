<?php

namespace LaminasUserTest\Form;

use LaminasUser\Form\Login as Form;

class LoginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers LaminasUser\Form\Login::__construct
     * @dataProvider providerTestConstruct
     */
    public function testConstruct($authIdentityFields = array())
    {
        $options = $this->getMock('LaminasUser\Options\AuthenticationOptionsInterface');
        $options->expects($this->once())
                ->method('getAuthIdentityFields')
                ->will($this->returnValue($authIdentityFields));

        $form = new Form(null, $options);

        $elements = $form->getElements();

        $this->assertArrayHasKey('identity', $elements);
        $this->assertArrayHasKey('credential', $elements);

        $expectedLabel="";
        if (count($authIdentityFields) > 0) {
            foreach ($authIdentityFields as $field) {
                $expectedLabel .= ($expectedLabel=="") ? '' : ' or ';
                $expectedLabel .= ucfirst($field);
                $this->assertContains(ucfirst($field), $elements['identity']->getLabel());
            }
        }

        $this->assertEquals($expectedLabel, $elements['identity']->getLabel());
    }

    /**
     * @covers LaminasUser\Form\Login::getAuthenticationOptions
     * @covers LaminasUser\Form\Login::setAuthenticationOptions
     */
    public function testSetGetAuthenticationOptions()
    {
        $options = $this->getMock('LaminasUser\Options\AuthenticationOptionsInterface');
        $options->expects($this->once())
                ->method('getAuthIdentityFields')
                ->will($this->returnValue(array()));
        $form = new Form(null, $options);

        $this->assertSame($options, $form->getAuthenticationOptions());
    }

    public function providerTestConstruct()
    {
        return array(
            array(array()),
            array(array('email')),
            array(array('username','email')),
        );
    }
}
