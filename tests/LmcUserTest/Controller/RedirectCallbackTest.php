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
     *
     *
     * @var RedirectCallback
     */
    protected $redirectCallback;

    /**
     *
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|ModuleOptions
     */
    protected $moduleOptions;

    /**
     *
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|RouteInterface
     */
    protected $router;

    /**
     *
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|Application
     */
    protected $application;

    /**
     *
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|Request
     */
    protected $request;

    /**
     *
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|Response
     */
    protected $response;

    /**
     *
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|MvcEvent
     */
    protected $mvcEvent;

    /**
     *
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|RouteMatch
     */
    protected $routeMatch;

    public function setUp():void
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

    public function testInvoke()
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
    public function testGetRedirectRouteFromRequest($get, $post, $getRouteExists, $postRouteExists)
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
            'LmcUser\Controller\RedirectCallback',
            'getRedirectRouteFromRequest'
        );
        $method->setAccessible(true);
        $result = $method->invoke($this->redirectCallback);

        $this->assertSame($expectedResult, $result);
    }

    public function providerGetRedirectRouteFromRequest()
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

    public function testRouteExistsRouteExists()
    {
        $route = 'existingRoute';

        $this->router->expects($this->once())
            ->method('assemble')
            ->with([], ['name' => $route]);

        $method = new \ReflectionMethod(
            'LmcUser\Controller\RedirectCallback',
            'routeExists'
        );
        $method->setAccessible(true);
        $result = $method->invoke($this->redirectCallback, $route);

        $this->assertTrue($result);
    }

    public function testRouteExistsRouteDoesntExists()
    {
        $route = 'existingRoute';

        $this->router->expects($this->once())
            ->method('assemble')
            ->with([], ['name' => $route])
            ->will($this->throwException(new \Laminas\Router\Exception\RuntimeException));

        $method = new \ReflectionMethod(
            'LmcUser\Controller\RedirectCallback',
            'routeExists'
        );
        $method->setAccessible(true);
        $result = $method->invoke($this->redirectCallback, $route);

        $this->assertFalse($result);
    }

    /**
     * @dataProvider providerGetRedirectNoRedirectParam
     */
    public function testGetRedirectNoRedirectParam($currentRoute, $optionsReturn, $expectedResult, $optionsMethod)
    {
        $this->moduleOptions->expects($this->once())
            ->method('getUseRedirectParameterIfPresent')
            ->will($this->returnValue(true));

        $this->router->expects($this->at(0))
            ->method('assemble');
        $this->router->expects($this->at(1))
            ->method('assemble')
            ->with([], ['name' => $optionsReturn])
            ->will($this->returnValue($expectedResult));

        if ($optionsMethod) {
            $this->moduleOptions->expects($this->never())
                ->method($optionsMethod)
                ->will($this->returnValue($optionsReturn));
        }
        $method = new \ReflectionMethod(
            'LmcUser\Controller\RedirectCallback',
            'getRedirect'
        );
        $method->setAccessible(true);
        $result = $method->invoke($this->redirectCallback, $currentRoute, $optionsReturn);

        $this->assertSame($expectedResult, $result);
    }

    public function providerGetRedirectNoRedirectParam()
    {
        return [
            ['lmcuser/login', 'lmcuser', '/user', 'getLoginRedirectRoute'],
            ['lmcuser/authenticate', 'lmcuser', '/user', 'getLoginRedirectRoute'],
            ['lmcuser/logout', 'lmcuser/login', '/user/login', 'getLogoutRedirectRoute'],
            ['testDefault', 'lmcuser', '/home', false],
        ];
    }

    public function testGetRedirectWithOptionOnButNoRedirect()
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
            'LmcUser\Controller\RedirectCallback',
            'getRedirect'
        );
        $method->setAccessible(true);
        $result = $method->invoke($this->redirectCallback, $route, $redirect);

        $this->assertSame($expectedResult, $result);
    }

    public function testGetRedirectWithOptionOnRedirectDoesntExists()
    {
        $route = 'lmcuser/login';
        $redirect = 'doesntExists';
        $expectedResult = '/user/login';

        $this->moduleOptions->expects($this->once())
            ->method('getUseRedirectParameterIfPresent')
            ->will($this->returnValue(true));

        $this->router->expects($this->at(0))
            ->method('assemble')
            ->with([], ['name' => $redirect])
            ->will($this->throwException(new \Laminas\Router\Exception\RuntimeException));

        $this->router->expects($this->at(1))
            ->method('assemble')
            ->with([], ['name' => $route])
            ->will($this->returnValue($expectedResult));

        $this->moduleOptions->expects($this->once())
            ->method('getLoginRedirectRoute')
            ->will($this->returnValue($route));

        $method = new \ReflectionMethod(
            'LmcUser\Controller\RedirectCallback',
            'getRedirect'
        );
        $method->setAccessible(true);
        $result = $method->invoke($this->redirectCallback, $route, $redirect);

        $this->assertSame($expectedResult, $result);
    }

    private function setUpApplication()
    {
        $this->request = $this->getMockBuilder('Laminas\Http\PhpEnvironment\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $this->response = $this->getMockBuilder('Laminas\Http\PhpEnvironment\Response')
            ->disableOriginalConstructor()
            ->getMock();

        $this->routeMatch = $this->getMockBuilder('Laminas\Router\RouteMatch')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mvcEvent = $this->getMockBuilder('Laminas\Mvc\MvcEvent')
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
