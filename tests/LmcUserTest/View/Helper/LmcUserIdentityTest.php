<?php

namespace LmcUserTest\View\Helper;

use LmcUser\View\Helper\LmcUserIdentity as ViewHelper;
use PHPUnit\Framework\TestCase;

class LmcUserIdentityTest extends TestCase
{
    protected $helper;

    protected $authService;

    public function setUp():void
    {
        $helper = new ViewHelper;
        $this->helper = $helper;

        $authService = $this->createMock('Laminas\Authentication\AuthenticationService');
        $this->authService = $authService;

        $helper->setAuthService($authService);
    }

    /**
     * @covers LmcUser\View\Helper\LmcUserIdentity::__invoke
     */
    public function testInvokeWithIdentity()
    {
        $this->authService->expects($this->once())
            ->method('hasIdentity')
            ->will($this->returnValue(true));
        $this->authService->expects($this->once())
            ->method('getIdentity')
            ->will($this->returnValue('lmcUser'));

        $result = $this->helper->__invoke();

        $this->assertEquals('lmcUser', $result);
    }

    /**
     * @covers LmcUser\View\Helper\LmcUserIdentity::__invoke
     */
    public function testInvokeWithoutIdentity()
    {
        $this->authService->expects($this->once())
            ->method('hasIdentity')
            ->will($this->returnValue(false));

        $result = $this->helper->__invoke();

        $this->assertFalse($result);
    }

    /**
     * @covers LmcUser\View\Helper\LmcUserIdentity::setAuthService
     * @covers LmcUser\View\Helper\LmcUserIdentity::getAuthService
     */
    public function testSetGetAuthService()
    {
        //We set the authservice in setUp, so we dont have to set it again
        $this->assertSame($this->authService, $this->helper->getAuthService());
    }
}
