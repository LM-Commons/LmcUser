<?php

namespace LmcUserTest\View\Helper;

use LmcUser\Exception\DomainException;
use LmcUser\View\Helper\LmcUserDisplayName as ViewHelper;
use PHPUnit\Framework\TestCase;

class LmcUserDisplayNameTest extends TestCase
{
    protected $helper;

    protected $authService;

    protected $user;

    public function setUp():void
    {
        $helper = new ViewHelper;
        $this->helper = $helper;

        $authService = $this->createMock('Laminas\Authentication\AuthenticationService');
        $this->authService = $authService;

        $user = $this->createMock('LmcUser\Entity\User');
        $this->user = $user;

        $helper->setAuthService($authService);
    }

    /**
     * @covers LmcUser\View\Helper\LmcUserDisplayName::__invoke
     */
    public function testInvokeWithoutUserAndNotLoggedIn()
    {
        $this->authService->expects($this->once())
            ->method('hasIdentity')
            ->willReturn(false);

        $result = $this->helper->__invoke(null);

        $this->assertFalse($result);
    }

    /**
     * @covers LmcUser\View\Helper\LmcUserDisplayName::__invoke
     */
    public function testInvokeWithoutUserButLoggedInWithWrongUserObject()
    {
        $this->expectException(DomainException::class);
        $this->authService->expects($this->once())
            ->method('hasIdentity')
            ->willReturn(true);
        $this->authService->expects($this->once())
            ->method('getIdentity')
            ->willReturn(new \StdClass);

        $this->helper->__invoke(null);
    }

    /**
     * @covers LmcUser\View\Helper\LmcUserDisplayName::__invoke
     */
    public function testInvokeWithoutUserButLoggedInWithDisplayName()
    {
        $this->user->expects($this->once())
            ->method('getDisplayName')
            ->willReturn('lmcUser');

        $this->authService->expects($this->once())
            ->method('hasIdentity')
            ->willReturn(true);
        $this->authService->expects($this->once())
            ->method('getIdentity')
            ->willReturn($this->user);

        $result = $this->helper->__invoke(null);

        $this->assertEquals('lmcUser', $result);
    }

    /**
     * @covers LmcUser\View\Helper\LmcUserDisplayName::__invoke
     */
    public function testInvokeWithoutUserButLoggedInWithoutDisplayNameButWithUsername()
    {
        $this->user->expects($this->once())
            ->method('getDisplayName')
            ->willReturn(null);
        $this->user->expects($this->once())
            ->method('getUsername')
            ->willReturn('lmcUser');

        $this->authService->expects($this->once())
            ->method('hasIdentity')
            ->willReturn(true);
        $this->authService->expects($this->once())
            ->method('getIdentity')
            ->willReturn($this->user);

        $result = $this->helper->__invoke(null);

        $this->assertEquals('lmcUser', $result);
    }

    /**
     * @covers LmcUser\View\Helper\LmcUserDisplayName::__invoke
     */
    public function testInvokeWithoutUserButLoggedInWithoutDisplayNameAndWithOutUsernameButWithEmail()
    {
        $this->user->expects($this->once())
            ->method('getDisplayName')
            ->willReturn(null);
        $this->user->expects($this->once())
            ->method('getUsername')
            ->willReturn(null);
        $this->user->expects($this->once())
            ->method('getEmail')
            ->willReturn('lmcUser@lmcUser.com');

        $this->authService->expects($this->once())
            ->method('hasIdentity')
            ->willReturn(true);
        $this->authService->expects($this->once())
            ->method('getIdentity')
            ->willReturn($this->user);

        $result = $this->helper->__invoke(null);

        $this->assertEquals('lmcUser', $result);
    }

    /**
     * @covers LmcUser\View\Helper\LmcUserDisplayName::setAuthService
     * @covers LmcUser\View\Helper\LmcUserDisplayName::getAuthService
     */
    public function testSetGetAuthService()
    {
        // We set the authservice in setUp, so we dont have to set it again
        $this->assertSame($this->authService, $this->helper->getAuthService());
    }
}
