<?php

namespace LaminasUserTest\View\Helper;

use LaminasUser\View\Helper\LaminasUserDisplayName as ViewHelper;

class LaminasUserDisplayNameTest extends \PHPUnit_Framework_TestCase
{
    protected $helper;

    protected $authService;

    protected $user;

    public function setUp()
    {
        $helper = new ViewHelper;
        $this->helper = $helper;

        $authService = $this->getMock('Laminas\Authentication\AuthenticationService');
        $this->authService = $authService;

        $user = $this->getMock('LaminasUser\Entity\User');
        $this->user = $user;

        $helper->setAuthService($authService);
    }

    /**
     * @covers LaminasUser\View\Helper\LaminasUserDisplayName::__invoke
     */
    public function testInvokeWithoutUserAndNotLoggedIn()
    {
        $this->authService->expects($this->once())
                          ->method('hasIdentity')
                          ->will($this->returnValue(false));

        $result = $this->helper->__invoke(null);

        $this->assertFalse($result);
    }

    /**
     * @covers LaminasUser\View\Helper\LaminasUserDisplayName::__invoke
     * @expectedException LaminasUser\Exception\DomainException
     */
    public function testInvokeWithoutUserButLoggedInWithWrongUserObject()
    {
        $this->authService->expects($this->once())
                          ->method('hasIdentity')
                          ->will($this->returnValue(true));
        $this->authService->expects($this->once())
                          ->method('getIdentity')
                          ->will($this->returnValue(new \StdClass));

        $this->helper->__invoke(null);
    }

    /**
     * @covers LaminasUser\View\Helper\LaminasUserDisplayName::__invoke
     */
    public function testInvokeWithoutUserButLoggedInWithDisplayName()
    {
        $this->user->expects($this->once())
                   ->method('getDisplayName')
                   ->will($this->returnValue('laminasUser'));

        $this->authService->expects($this->once())
                          ->method('hasIdentity')
                          ->will($this->returnValue(true));
        $this->authService->expects($this->once())
                          ->method('getIdentity')
                          ->will($this->returnValue($this->user));

        $result = $this->helper->__invoke(null);

        $this->assertEquals('laminasUser', $result);
    }

    /**
     * @covers LaminasUser\View\Helper\LaminasUserDisplayName::__invoke
     */
    public function testInvokeWithoutUserButLoggedInWithoutDisplayNameButWithUsername()
    {
        $this->user->expects($this->once())
                   ->method('getDisplayName')
                   ->will($this->returnValue(null));
        $this->user->expects($this->once())
                   ->method('getUsername')
                   ->will($this->returnValue('laminasUser'));

        $this->authService->expects($this->once())
                          ->method('hasIdentity')
                          ->will($this->returnValue(true));
        $this->authService->expects($this->once())
                          ->method('getIdentity')
                          ->will($this->returnValue($this->user));

        $result = $this->helper->__invoke(null);

        $this->assertEquals('laminasUser', $result);
    }

    /**
     * @covers LaminasUser\View\Helper\LaminasUserDisplayName::__invoke
     */
    public function testInvokeWithoutUserButLoggedInWithoutDisplayNameAndWithOutUsernameButWithEmail()
    {
        $this->user->expects($this->once())
                   ->method('getDisplayName')
                   ->will($this->returnValue(null));
        $this->user->expects($this->once())
                   ->method('getUsername')
                   ->will($this->returnValue(null));
        $this->user->expects($this->once())
                   ->method('getEmail')
                   ->will($this->returnValue('laminasUser@laminasUser.com'));

        $this->authService->expects($this->once())
                          ->method('hasIdentity')
                          ->will($this->returnValue(true));
        $this->authService->expects($this->once())
                          ->method('getIdentity')
                          ->will($this->returnValue($this->user));

        $result = $this->helper->__invoke(null);

        $this->assertEquals('laminasUser', $result);
    }

    /**
     * @covers LaminasUser\View\Helper\LaminasUserDisplayName::setAuthService
     * @covers LaminasUser\View\Helper\LaminasUserDisplayName::getAuthService
     */
    public function testSetGetAuthService()
    {
        // We set the authservice in setUp, so we dont have to set it again
        $this->assertSame($this->authService, $this->helper->getAuthService());
    }
}
