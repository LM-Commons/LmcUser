<?php

namespace LmcUserTest\Controller;

use Laminas\Http\PhpEnvironment\Request;
use Laminas\Http\PhpEnvironment\Response;
use Laminas\Mvc\Application;
use Laminas\Mvc\MvcEvent;
use Laminas\Router\RouteInterface;
use Laminas\Router\RouteMatch;
use LmcUser\Controller\RedirectCallback;
use LmcUser\Options\ModuleOptions;
use PHPUnit\Framework\TestCase;

class RedirectCallbackTest extends TestCase
{
    /**
     * @var RedirectCallback
     */
    protected $redirectCallback;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ModuleOptions
     */
    protected $moduleOptions;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|RouteInterface
     */
    protected $router;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|Application
     */
    protected $application;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|Request
     */
    protected $request;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|Response
     */
    protected $response;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|MvcEvent
     */
    protected $mvcEvent;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|RouteMatch
     */
    protected $routeMatch;

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $this->router = $this->getMockBuilder('Laminas\Router\RouteInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->moduleOptions = $this->getMockBuilder('LmcUser\Options\ModuleOptions')
            ->disableOriginalConstructor()
            ->getMock();

        $this->application = $this->getMockBuilder('Laminas\Mvc\Application')
            ->disableOriginalConstructor()
            ->getMock();
        $this->setUpApplication();

        $this->redirectCallback = new RedirectCallback(
            $this->application,
            $this->router,
            $this->moduleOptions
        );
    }

    public function testInvoke(): void
    {
        $url = 'someUrl';

        $this->routeMatch->expects($this->once())
            ->method('getMatchedRouteName')
            ->will($this->returnValue('someRoute'));

        $headers = $this->createMock('Laminas\Http\Headers');
        $headers->expects($this->once())
            ->method('addHeaderLine')
            ->with('Location', $url);

        $this->router->expects($this->any())
            ->method('assemble')
            ->with([], ['name' => 'lmcuser'])
            ->will($this->returnValue($url));

        $this->response->expects($this->once())
            ->method('getHeaders')
            ->will($this->returnValue($headers));

        $this->response->expects($this->once())
            ->method('setStatusCode')
            ->with(302);

        $result = $this->redirectCallback->__invoke();

        $this->assertSame($this->response, $result);
    }

    /**
     * @dataProvider providerGetRedirectRouteFromRequest
     */
    public function testGetRedirectRouteFromRequest($get, $post, $getRouteExists, $postRouteExists): void
    {
        $expectedResult = false;

        $this->request->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($get));

        if ($get) {
            $this->router->expects($this->any())
                ->method('assemble')
                ->with([], ['name' => $get])
                ->will($getRouteExists);

            if ($getRouteExists == $this->returnValue(true)) {
                $expectedResult = $get;
            }
        }

        if (!$get || !$getRouteExists) {
            $this->request->expects($this->once())
                ->method('getPost')
                ->will($this->returnValue($post));

            if ($post) {
                $this->router->expects($this->any())
                    ->method('assemble')
                    ->with([], ['name' => $post])
                    ->will($postRouteExists);

                if ($postRouteExists == $this->returnValue(true)) {
                    $expectedResult = $post;
                }
            }
        }

        $method = new \ReflectionMethod(
            RedirectCallback::class,
            'getRedirectRouteFromRequest'
        );
        $method->setAccessible(true);
        $result = $method->invoke($this->redirectCallback);

        $this->assertSame($expectedResult, $result);
    }

    public function providerGetRedirectRouteFromRequest(): array
    {
        return [
            ['user', false, $this->returnValue('route'), false],
            ['user', false, $this->returnValue('route'), $this->returnValue(true)],
            ['user', 'user', $this->returnValue('route'), $this->returnValue(true)],
            ['user', 'user', $this->throwException(new \Laminas\Router\Exception\RuntimeException), $this->returnValue(true)],
            ['user', 'user', $this->throwException(new \Laminas\Router\Exception\RuntimeException), $this->throwException(new \Laminas\Router\Exception\RuntimeException)],
            [false, 'user', false, $this->returnValue(true)],
            [false, 'user', false, $this->throwException(new \Laminas\Router\Exception\RuntimeException)],
            [false, 'user', false, $this->throwException(new \Laminas\Router\Exception\RuntimeException)],
        ];
    }

    public function testRouteExistsRouteExists(): void
    {
        $route = 'existingRoute';

        $this->router->expects($this->once())
            ->method('assemble')
            ->with([], ['name' => $route]);

        $method = new \ReflectionMethod(
            RedirectCallback::class,
            'routeExists'
        );
        $method->setAccessible(true);
        $result = $method->invoke($this->redirectCallback, $route);

        $this->assertTrue($result);
    }

    public function testRouteExistsRouteDoesntExists(): void
    {
        $route = 'existingRoute';

        $this->router->expects($this->once())
            ->method('assemble')
            ->with([], ['name' => $route])
            ->will($this->throwException(new \Laminas\Router\Exception\RuntimeException));

        $method = new \ReflectionMethod(
            RedirectCallback::class,
            'routeExists'
        );
        $method->setAccessible(true);
        $result = $method->invoke($this->redirectCallback, $route);

        $this->assertFalse($result);
    }

    public function testRouteMatchedRouteMatched(): void
    {
        $route = 'existingRoute';

        $routeMatch = $this->getMockBuilder(RouteMatch::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->router->expects($this->once())
            ->method('match')
            ->willReturn($routeMatch);

        $method = new \ReflectionMethod(
            RedirectCallback::class,
            'routeMatched'
        );
        $method->setAccessible(true);
        $result = $method->invoke($this->redirectCallback, $route);

        $this->assertTrue($result);
    }

    public function testRouteMatchedRouteNotMatched(): void
    {
        $route = 'existingRoute';

        $this->router->expects($this->once())
            ->method('match')
            ->willReturn(null);

        $method = new \ReflectionMethod(
            RedirectCallback::class,
            'routeMatched'
        );
        $method->setAccessible(true);
        $result = $method->invoke($this->redirectCallback, $route);

        $this->assertFalse($result);
    }

    /**
     * @dataProvider providerGetRedirectNoRedirectParam
     */
    public function testGetRedirectNoRedirectParam(
        string $currentRoute,
        string $redirect,
        string $expectedResult,
        bool $routeMatch
    ): void {
        $this->moduleOptions->expects($this->once())
            ->method('getUseRedirectParameterIfPresent')
            ->will($this->returnValue(true));

        if ($routeMatch) {
            $routeMatch = $this->getMockBuilder(RouteMatch::class)
                ->disableOriginalConstructor()
                ->getMock();
        } else {
            $routeMatch = null;
        }

        $this->router->expects($this->once())
            ->method('match')
            ->willReturn($routeMatch);

        $this->router->expects($routeMatch ? $this->never() : $this->exactly(2))
            ->method('assemble')
            ->with([], ['name' => $redirect])
            ->will($this->returnValue($expectedResult));

        $method = new \ReflectionMethod(
            RedirectCallback::class,
            'getRedirect'
        );
        $method->setAccessible(true);
        $result = $method->invoke($this->redirectCallback, $currentRoute, $redirect);

        $this->assertSame($expectedResult, $result);
    }

    public function providerGetRedirectNoRedirectParam(): array
    {
        return [
            ['lmcuser/login', 'lmcuser', '/user', false],
            ['lmcuser/authenticate', 'lmcuser', '/user', false],
            ['lmcuser/logout', 'lmcuser/login', '/user/login', false],
            ['testDefault', 'lmcuser', '/home', false],
            ['lmcuser/authenticate', '/some/route', '/some/route', true],
        ];
    }

    public function testGetRedirectWithOptionOnButNoRedirect(): void
    {
        $route = 'lmcuser/login';
        $redirect = false;
        $expectedResult = '/user/login';

        $this->moduleOptions->expects($this->once())
            ->method('getUseRedirectParameterIfPresent')
            ->will($this->returnValue(true));

        $this->moduleOptions->expects($this->once())
            ->method('getLoginRedirectRoute')
            ->will($this->returnValue($route));

        $this->router->expects($this->once())
            ->method('assemble')
            ->with([], ['name' => $route])
            ->will($this->returnValue($expectedResult));

        $method = new \ReflectionMethod(
            RedirectCallback::class,
            'getRedirect'
        );
        $method->setAccessible(true);
        $result = $method->invoke($this->redirectCallback, $route, $redirect);

        $this->assertSame($expectedResult, $result);
    }

    public function testGetRedirectWithOptionOnRedirectDoesntExist(): void
    {
        $route = 'lmcuser/login';
        $redirect = 'doesntExists';
        $expectedResult = '/user/login';

        $this->moduleOptions->expects($this->once())
            ->method('getUseRedirectParameterIfPresent')
            ->will($this->returnValue(true));

        $this->router->expects($this->once())
            ->method('match')
            ->willReturn(null);

        $this->router->expects($this->at(1))
            ->method('assemble')
            ->with([], ['name' => $redirect])
            ->will($this->throwException(new \Laminas\Router\Exception\RuntimeException));

        $this->router->expects($this->at(2))
            ->method('assemble')
            ->with([], ['name' => $route])
            ->will($this->returnValue($expectedResult));

        $this->moduleOptions->expects($this->once())
            ->method('getLoginRedirectRoute')
            ->will($this->returnValue($route));

        $method = new \ReflectionMethod(
            RedirectCallback::class,
            'getRedirect'
        );
        $method->setAccessible(true);
        $result = $method->invoke($this->redirectCallback, $route, $redirect);

        $this->assertSame($expectedResult, $result);
    }

    private function setUpApplication(): void
    {
        $this->request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->routeMatch = $this->getMockBuilder(RouteMatch::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mvcEvent = $this->getMockBuilder(MvcEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->mvcEvent->expects($this->any())
            ->method('getRouteMatch')
            ->will($this->returnValue($this->routeMatch));


        $this->application->expects($this->any())
            ->method('getMvcEvent')
            ->will($this->returnValue($this->mvcEvent));
        $this->application->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($this->request));
        $this->application->expects($this->any())
            ->method('getResponse')
            ->will($this->returnValue($this->response));
    }
}
