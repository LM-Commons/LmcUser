<?php

namespace LmcUserTest\Options;

use LmcUser\Options\ModuleOptions as Options;
use PHPUnit\Framework\TestCase;

class ModuleOptionsTest extends TestCase
{
    /**
     * @var Options $options
     */
    protected $options;

    public function setUp():void
    {
        $options = new Options;
        $this->options = $options;
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getLoginRedirectRoute
     * @covers LmcUser\Options\ModuleOptions::setLoginRedirectRoute
     */
    public function testSetGetLoginRedirectRoute()
    {
        $this->options->setLoginRedirectRoute('lmcUserRoute');
        $this->assertEquals('lmcUserRoute', $this->options->getLoginRedirectRoute());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getLoginRedirectRoute
     */
    public function testGetLoginRedirectRoute()
    {
        $this->assertEquals('lmcuser', $this->options->getLoginRedirectRoute());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getLogoutRedirectRoute
     * @covers LmcUser\Options\ModuleOptions::setLogoutRedirectRoute
     */
    public function testSetGetLogoutRedirectRoute()
    {
        $this->options->setLogoutRedirectRoute('lmcUserRoute');
        $this->assertEquals('lmcUserRoute', $this->options->getLogoutRedirectRoute());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getLogoutRedirectRoute
     */
    public function testGetLogoutRedirectRoute()
    {
        $this->assertSame('lmcuser/login', $this->options->getLogoutRedirectRoute());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getUseRedirectParameterIfPresent
     * @covers LmcUser\Options\ModuleOptions::setUseRedirectParameterIfPresent
     */
    public function testSetGetUseRedirectParameterIfPresent()
    {
        $this->options->setUseRedirectParameterIfPresent(false);
        $this->assertFalse($this->options->getUseRedirectParameterIfPresent());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getUseRedirectParameterIfPresent
     */
    public function testGetUseRedirectParameterIfPresent()
    {
        $this->assertTrue($this->options->getUseRedirectParameterIfPresent());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getUserLoginWidgetViewTemplate
     * @covers LmcUser\Options\ModuleOptions::setUserLoginWidgetViewTemplate
     */
    public function testSetGetUserLoginWidgetViewTemplate()
    {
        $this->options->setUserLoginWidgetViewTemplate('lmcUser.phtml');
        $this->assertEquals('lmcUser.phtml', $this->options->getUserLoginWidgetViewTemplate());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getUserLoginWidgetViewTemplate
     */
    public function testGetUserLoginWidgetViewTemplate()
    {
        $this->assertEquals('lmc-user/user/login.phtml', $this->options->getUserLoginWidgetViewTemplate());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getEnableRegistration
     * @covers LmcUser\Options\ModuleOptions::setEnableRegistration
     */
    public function testSetGetEnableRegistration()
    {
        $this->options->setEnableRegistration(false);
        $this->assertFalse($this->options->getEnableRegistration());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getEnableRegistration
     */
    public function testGetEnableRegistration()
    {
        $this->assertTrue($this->options->getEnableRegistration());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getLoginFormTimeout
     * @covers LmcUser\Options\ModuleOptions::setLoginFormTimeout
     */
    public function testSetGetLoginFormTimeout()
    {
        $this->options->setLoginFormTimeout(100);
        $this->assertEquals(100, $this->options->getLoginFormTimeout());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getLoginFormTimeout
     */
    public function testGetLoginFormTimeout()
    {
        $this->assertEquals(300, $this->options->getLoginFormTimeout());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getUserFormTimeout
     * @covers LmcUser\Options\ModuleOptions::setUserFormTimeout
     */
    public function testSetGetUserFormTimeout()
    {
        $this->options->setUserFormTimeout(100);
        $this->assertEquals(100, $this->options->getUserFormTimeout());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getUserFormTimeout
     */
    public function testGetUserFormTimeout()
    {
        $this->assertEquals(300, $this->options->getUserFormTimeout());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getLoginAfterRegistration
     * @covers LmcUser\Options\ModuleOptions::setLoginAfterRegistration
     */
    public function testSetGetLoginAfterRegistration()
    {
        $this->options->setLoginAfterRegistration(false);
        $this->assertFalse($this->options->getLoginAfterRegistration());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getLoginAfterRegistration
     */
    public function testGetLoginAfterRegistration()
    {
        $this->assertTrue($this->options->getLoginAfterRegistration());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getEnableUserState
     * @covers LmcUser\Options\ModuleOptions::setEnableUserState
     */
    public function testSetGetEnableUserState()
    {
        $this->options->setEnableUserState(true);
        $this->assertTrue($this->options->getEnableUserState());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getEnableUserState
     */
    public function testGetEnableUserState()
    {
        $this->assertFalse($this->options->getEnableUserState());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getDefaultUserState
     */
    public function testGetDefaultUserState()
    {
        $this->assertEquals(1, $this->options->getDefaultUserState());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getDefaultUserState
     * @covers LmcUser\Options\ModuleOptions::setDefaultUserState
     */
    public function testSetGetDefaultUserState()
    {
        $this->options->setDefaultUserState(3);
        $this->assertEquals(3, $this->options->getDefaultUserState());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getAllowedLoginStates
     */
    public function testGetAllowedLoginStates()
    {
        $this->assertEquals(array(null, 1), $this->options->getAllowedLoginStates());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getAllowedLoginStates
     * @covers LmcUser\Options\ModuleOptions::setAllowedLoginStates
     */
    public function testSetGetAllowedLoginStates()
    {
        $this->options->setAllowedLoginStates(array(2, 5, null));
        $this->assertEquals(array(2, 5, null), $this->options->getAllowedLoginStates());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getAuthAdapters
     */
    public function testGetAuthAdapters()
    {
        $this->assertEquals(array(100 => 'LmcUser\Authentication\Adapter\Db'), $this->options->getAuthAdapters());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getAuthAdapters
     * @covers LmcUser\Options\ModuleOptions::setAuthAdapters
     */
    public function testSetGetAuthAdapters()
    {
        $this->options->setAuthAdapters(array(40 => 'SomeAdapter'));
        $this->assertEquals(array(40 => 'SomeAdapter'), $this->options->getAuthAdapters());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getAuthIdentityFields
     * @covers LmcUser\Options\ModuleOptions::setAuthIdentityFields
     */
    public function testSetGetAuthIdentityFields()
    {
        $this->options->setAuthIdentityFields(array('username'));
        $this->assertEquals(array('username'), $this->options->getAuthIdentityFields());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getAuthIdentityFields
     */
    public function testGetAuthIdentityFields()
    {
        $this->assertEquals(array('email'), $this->options->getAuthIdentityFields());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getEnableUsername
     */
    public function testGetEnableUsername()
    {
        $this->assertFalse($this->options->getEnableUsername());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getEnableUsername
     * @covers LmcUser\Options\ModuleOptions::setEnableUsername
     */
    public function testSetGetEnableUsername()
    {
        $this->options->setEnableUsername(true);
        $this->assertTrue($this->options->getEnableUsername());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getEnableDisplayName
     * @covers LmcUser\Options\ModuleOptions::setEnableDisplayName
     */
    public function testSetGetEnableDisplayName()
    {
        $this->options->setEnableDisplayName(true);
        $this->assertTrue($this->options->getEnableDisplayName());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getEnableDisplayName
     */
    public function testGetEnableDisplayName()
    {
        $this->assertFalse($this->options->getEnableDisplayName());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getUseRegistrationFormCaptcha
     * @covers LmcUser\Options\ModuleOptions::setUseRegistrationFormCaptcha
     */
    public function testSetGetUseRegistrationFormCaptcha()
    {
        $this->options->setUseRegistrationFormCaptcha(true);
        $this->assertTrue($this->options->getUseRegistrationFormCaptcha());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getUseRegistrationFormCaptcha
     */
    public function testGetUseRegistrationFormCaptcha()
    {
        $this->assertFalse($this->options->getUseRegistrationFormCaptcha());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getUserEntityClass
     * @covers LmcUser\Options\ModuleOptions::setUserEntityClass
     */
    public function testSetGetUserEntityClass()
    {
        $this->options->setUserEntityClass('lmcUser');
        $this->assertEquals('lmcUser', $this->options->getUserEntityClass());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getUserEntityClass
     */
    public function testGetUserEntityClass()
    {
        $this->assertEquals('LmcUser\Entity\User', $this->options->getUserEntityClass());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getPasswordCost
     * @covers LmcUser\Options\ModuleOptions::setPasswordCost
     */
    public function testSetGetPasswordCost()
    {
        $this->options->setPasswordCost(10);
        $this->assertEquals(10, $this->options->getPasswordCost());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getPasswordCost
     */
    public function testGetPasswordCost()
    {
        $this->assertEquals(14, $this->options->getPasswordCost());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getTableName
     * @covers LmcUser\Options\ModuleOptions::setTableName
     */
    public function testSetGetTableName()
    {
        $this->options->setTableName('lmcUser');
        $this->assertEquals('lmcUser', $this->options->getTableName());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getTableName
     */
    public function testGetTableName()
    {
        $this->assertEquals('user', $this->options->getTableName());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getFormCaptchaOptions
     * @covers LmcUser\Options\ModuleOptions::setFormCaptchaOptions
     */
    public function testSetGetFormCaptchaOptions()
    {
        $expected = array(
            'class'   => 'someClass',
            'options' => array(
                'anOption' => 3,
            ),
        );
        $this->options->setFormCaptchaOptions($expected);
        $this->assertEquals($expected, $this->options->getFormCaptchaOptions());
    }

    /**
     * @covers LmcUser\Options\ModuleOptions::getFormCaptchaOptions
     */
    public function testGetFormCaptchaOptions()
    {
        $expected = array(
            'class'   => 'figlet',
            'options' => array(
                'wordLen'    => 5,
                'expiration' => 300,
                'timeout'    => 300,
            ),
        );
        $this->assertEquals($expected, $this->options->getFormCaptchaOptions());
    }
}
