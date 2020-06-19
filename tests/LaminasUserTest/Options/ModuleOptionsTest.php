<?php

namespace LaminasUserTest\Options;

use LaminasUser\Options\ModuleOptions as Options;

class ModuleOptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Options $options
     */
    protected $options;

    public function setUp()
    {
        $options = new Options;
        $this->options = $options;
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getLoginRedirectRoute
     * @covers LaminasUser\Options\ModuleOptions::setLoginRedirectRoute
     */
    public function testSetGetLoginRedirectRoute()
    {
        $this->options->setLoginRedirectRoute('zfcUserRoute');
        $this->assertEquals('zfcUserRoute', $this->options->getLoginRedirectRoute());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getLoginRedirectRoute
     */
    public function testGetLoginRedirectRoute()
    {
        $this->assertEquals('zfcuser', $this->options->getLoginRedirectRoute());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getLogoutRedirectRoute
     * @covers LaminasUser\Options\ModuleOptions::setLogoutRedirectRoute
     */
    public function testSetGetLogoutRedirectRoute()
    {
        $this->options->setLogoutRedirectRoute('zfcUserRoute');
        $this->assertEquals('zfcUserRoute', $this->options->getLogoutRedirectRoute());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getLogoutRedirectRoute
     */
    public function testGetLogoutRedirectRoute()
    {
        $this->assertSame('zfcuser/login', $this->options->getLogoutRedirectRoute());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getUseRedirectParameterIfPresent
     * @covers LaminasUser\Options\ModuleOptions::setUseRedirectParameterIfPresent
     */
    public function testSetGetUseRedirectParameterIfPresent()
    {
        $this->options->setUseRedirectParameterIfPresent(false);
        $this->assertFalse($this->options->getUseRedirectParameterIfPresent());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getUseRedirectParameterIfPresent
     */
    public function testGetUseRedirectParameterIfPresent()
    {
        $this->assertTrue($this->options->getUseRedirectParameterIfPresent());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getUserLoginWidgetViewTemplate
     * @covers LaminasUser\Options\ModuleOptions::setUserLoginWidgetViewTemplate
     */
    public function testSetGetUserLoginWidgetViewTemplate()
    {
        $this->options->setUserLoginWidgetViewTemplate('zfcUser.phtml');
        $this->assertEquals('zfcUser.phtml', $this->options->getUserLoginWidgetViewTemplate());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getUserLoginWidgetViewTemplate
     */
    public function testGetUserLoginWidgetViewTemplate()
    {
        $this->assertEquals('zfc-user/user/login.phtml', $this->options->getUserLoginWidgetViewTemplate());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getEnableRegistration
     * @covers LaminasUser\Options\ModuleOptions::setEnableRegistration
     */
    public function testSetGetEnableRegistration()
    {
        $this->options->setEnableRegistration(false);
        $this->assertFalse($this->options->getEnableRegistration());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getEnableRegistration
     */
    public function testGetEnableRegistration()
    {
        $this->assertTrue($this->options->getEnableRegistration());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getLoginFormTimeout
     * @covers LaminasUser\Options\ModuleOptions::setLoginFormTimeout
     */
    public function testSetGetLoginFormTimeout()
    {
        $this->options->setLoginFormTimeout(100);
        $this->assertEquals(100, $this->options->getLoginFormTimeout());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getLoginFormTimeout
     */
    public function testGetLoginFormTimeout()
    {
        $this->assertEquals(300, $this->options->getLoginFormTimeout());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getUserFormTimeout
     * @covers LaminasUser\Options\ModuleOptions::setUserFormTimeout
     */
    public function testSetGetUserFormTimeout()
    {
        $this->options->setUserFormTimeout(100);
        $this->assertEquals(100, $this->options->getUserFormTimeout());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getUserFormTimeout
     */
    public function testGetUserFormTimeout()
    {
        $this->assertEquals(300, $this->options->getUserFormTimeout());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getLoginAfterRegistration
     * @covers LaminasUser\Options\ModuleOptions::setLoginAfterRegistration
     */
    public function testSetGetLoginAfterRegistration()
    {
        $this->options->setLoginAfterRegistration(false);
        $this->assertFalse($this->options->getLoginAfterRegistration());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getLoginAfterRegistration
     */
    public function testGetLoginAfterRegistration()
    {
        $this->assertTrue($this->options->getLoginAfterRegistration());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getEnableUserState
     * @covers LaminasUser\Options\ModuleOptions::setEnableUserState
     */
    public function testSetGetEnableUserState()
    {
        $this->options->setEnableUserState(true);
        $this->assertTrue($this->options->getEnableUserState());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getEnableUserState
     */
    public function testGetEnableUserState()
    {
        $this->assertFalse($this->options->getEnableUserState());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getDefaultUserState
     */
    public function testGetDefaultUserState()
    {
        $this->assertEquals(1, $this->options->getDefaultUserState());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getDefaultUserState
     * @covers LaminasUser\Options\ModuleOptions::setDefaultUserState
     */
    public function testSetGetDefaultUserState()
    {
        $this->options->setDefaultUserState(3);
        $this->assertEquals(3, $this->options->getDefaultUserState());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getAllowedLoginStates
     */
    public function testGetAllowedLoginStates()
    {
        $this->assertEquals(array(null, 1), $this->options->getAllowedLoginStates());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getAllowedLoginStates
     * @covers LaminasUser\Options\ModuleOptions::setAllowedLoginStates
     */
    public function testSetGetAllowedLoginStates()
    {
        $this->options->setAllowedLoginStates(array(2, 5, null));
        $this->assertEquals(array(2, 5, null), $this->options->getAllowedLoginStates());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getAuthAdapters
     */
    public function testGetAuthAdapters()
    {
        $this->assertEquals(array(100 => 'LaminasUser\Authentication\Adapter\Db'), $this->options->getAuthAdapters());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getAuthAdapters
     * @covers LaminasUser\Options\ModuleOptions::setAuthAdapters
     */
    public function testSetGetAuthAdapters()
    {
        $this->options->setAuthAdapters(array(40 => 'SomeAdapter'));
        $this->assertEquals(array(40 => 'SomeAdapter'), $this->options->getAuthAdapters());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getAuthIdentityFields
     * @covers LaminasUser\Options\ModuleOptions::setAuthIdentityFields
     */
    public function testSetGetAuthIdentityFields()
    {
        $this->options->setAuthIdentityFields(array('username'));
        $this->assertEquals(array('username'), $this->options->getAuthIdentityFields());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getAuthIdentityFields
     */
    public function testGetAuthIdentityFields()
    {
        $this->assertEquals(array('email'), $this->options->getAuthIdentityFields());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getEnableUsername
     */
    public function testGetEnableUsername()
    {
        $this->assertFalse($this->options->getEnableUsername());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getEnableUsername
     * @covers LaminasUser\Options\ModuleOptions::setEnableUsername
     */
    public function testSetGetEnableUsername()
    {
        $this->options->setEnableUsername(true);
        $this->assertTrue($this->options->getEnableUsername());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getEnableDisplayName
     * @covers LaminasUser\Options\ModuleOptions::setEnableDisplayName
     */
    public function testSetGetEnableDisplayName()
    {
        $this->options->setEnableDisplayName(true);
        $this->assertTrue($this->options->getEnableDisplayName());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getEnableDisplayName
     */
    public function testGetEnableDisplayName()
    {
        $this->assertFalse($this->options->getEnableDisplayName());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getUseRegistrationFormCaptcha
     * @covers LaminasUser\Options\ModuleOptions::setUseRegistrationFormCaptcha
     */
    public function testSetGetUseRegistrationFormCaptcha()
    {
        $this->options->setUseRegistrationFormCaptcha(true);
        $this->assertTrue($this->options->getUseRegistrationFormCaptcha());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getUseRegistrationFormCaptcha
     */
    public function testGetUseRegistrationFormCaptcha()
    {
        $this->assertFalse($this->options->getUseRegistrationFormCaptcha());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getUserEntityClass
     * @covers LaminasUser\Options\ModuleOptions::setUserEntityClass
     */
    public function testSetGetUserEntityClass()
    {
        $this->options->setUserEntityClass('zfcUser');
        $this->assertEquals('zfcUser', $this->options->getUserEntityClass());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getUserEntityClass
     */
    public function testGetUserEntityClass()
    {
        $this->assertEquals('LaminasUser\Entity\User', $this->options->getUserEntityClass());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getPasswordCost
     * @covers LaminasUser\Options\ModuleOptions::setPasswordCost
     */
    public function testSetGetPasswordCost()
    {
        $this->options->setPasswordCost(10);
        $this->assertEquals(10, $this->options->getPasswordCost());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getPasswordCost
     */
    public function testGetPasswordCost()
    {
        $this->assertEquals(14, $this->options->getPasswordCost());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getTableName
     * @covers LaminasUser\Options\ModuleOptions::setTableName
     */
    public function testSetGetTableName()
    {
        $this->options->setTableName('zfcUser');
        $this->assertEquals('zfcUser', $this->options->getTableName());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getTableName
     */
    public function testGetTableName()
    {
        $this->assertEquals('user', $this->options->getTableName());
    }

    /**
     * @covers LaminasUser\Options\ModuleOptions::getFormCaptchaOptions
     * @covers LaminasUser\Options\ModuleOptions::setFormCaptchaOptions
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
     * @covers LaminasUser\Options\ModuleOptions::getFormCaptchaOptions
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
