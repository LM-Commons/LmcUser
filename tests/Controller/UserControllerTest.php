<?php

namespace LmcUserTest\Controller;

use Laminas\Form\FormElementManager;
use LmcUser\Controller\RedirectCallback;
use LmcUser\Controller\UserController as Controller;
use Laminas\Http\Response;
use Laminas\Stdlib\Parameters;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LmcUser\Service\User as UserService;
use Laminas\Form\Form;
use LmcUser\Options\ModuleOptions;
use LmcUser\Entity\User as UserIdentity;
use PHPUnit\Framework\TestCase;

class UserControllerTest extends TestCase
{
    /**
     * @var Controller $controller
     */
    protected $controller;

    protected $pluginManager;

    public $pluginManagerPlugins = array();

    protected $lmcUserAuthenticationPlugin;

    protected $options;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|RedirectCallback
     */
    protected $redirectCallback;

    public function setUp():void
    {
        $this->redirectCallback = $this->getMockBuilder('LmcUser\Controller\RedirectCallback')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = new Controller($this->redirectCallback);
        $this->controller = $controller;

        $this->lmcUserAuthenticationPlugin = $this->createMock('LmcUser\Controller\Plugin\LmcUserAuthentication');

        $pluginManager = $this->getMockBuilder('Laminas\Mvc\Controller\PluginManager')
            ->disableOriginalConstructor()
            ->getMock();

        $pluginManager->expects($this->any())
            ->method('get')
            ->willReturnCallback(array($this, 'helperMockCallbackPluginManagerGet'));

        $this->pluginManager = $pluginManager;

        $options = $this->createMock('LmcUser\Options\ModuleOptions');
        $this->options = $options;

        $controller->setPluginManager($pluginManager);
        $controller->setOptions($options);
    }

    public function setUpLmcUserAuthenticationPlugin($option)
    {
        if (array_key_exists('hasIdentity', $option)) {
            $return = (is_callable($option['hasIdentity']))
                ? $this->returnCallback($option['hasIdentity'])
                : $this->returnValue($option['hasIdentity']);
            $this->lmcUserAuthenticationPlugin->expects($this->any())
                ->method('hasIdentity')
                ->will($return);
        }

        if (array_key_exists('getAuthAdapter', $option)) {
            $return = (is_callable($option['getAuthAdapter']))
                ? $this->returnCallback($option['getAuthAdapter'])
                : $this->returnValue($option['getAuthAdapter']);

            $this->lmcUserAuthenticationPlugin->expects($this->any())
                ->method('getAuthAdapter')
                ->will($return);
        }

        if (array_key_exists('getAuthService', $option)) {
            $return = (is_callable($option['getAuthService']))
                ? $this->returnCallback($option['getAuthService'])
                : $this->returnValue($option['getAuthService']);

            $this->lmcUserAuthenticationPlugin->expects($this->any())
                ->method('getAuthService')
                ->will($return);
        }

        $this->pluginManagerPlugins['lmcUserAuthentication'] = $this->lmcUserAuthenticationPlugin;

        return $this->lmcUserAuthenticationPlugin;
    }

    /**
     * @dataProvider providerTestActionControllHasIdentity
     */
    public function testActionControllHasIdentity($methodeName, $hasIdentity, $redirectRoute, $optionGetter)
    {
        $controller = $this->controller;
        $redirectRoute = $redirectRoute ?: $controller::ROUTE_LOGIN;

        $this->setUpLmcUserAuthenticationPlugin(
            array(
            'hasIdentity'=>$hasIdentity
            )
        );

        $response = new Response();

        if ($optionGetter) {
            $this->options->expects($this->once())
                ->method($optionGetter)
                ->willReturn($redirectRoute);
        }

        $redirect = $this->createMock('Laminas\Mvc\Controller\Plugin\Redirect');
        $redirect->expects($this->once())
            ->method('toRoute')
            ->with($redirectRoute)
            ->willReturn($response);

        $this->pluginManagerPlugins['redirect']= $redirect;

        $result = call_user_func(array($controller, $methodeName));

        $this->assertInstanceOf('Laminas\Http\Response', $result);
        $this->assertSame($response, $result);
    }

    /**
     * @depend testActionControllHasIdentity
     */
    public function testIndexActionLoggedIn()
    {
        $controller = $this->controller;
        $this->setUpLmcUserAuthenticationPlugin(
            array(
            'hasIdentity'=>true
            )
        );

        $result = $controller->indexAction();

        $this->assertInstanceOf('Laminas\View\Model\ViewModel', $result);
    }


    /**
     * @dataProvider providerTrueOrFalseX2
     * @depend       testActionControllHasIdentity
     */
    public function testLoginActionValidFormRedirectFalse($isValid, $wantRedirect)
    {
        $controller = $this->controller;
        $redirectUrl = 'localhost/redirect1';

        $plugin = $this->setUpLmcUserAuthenticationPlugin(
            array(
            'hasIdentity'=>false
            )
        );

        $flashMessenger = $this->createMock(
            'Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger'
        );
        $this->pluginManagerPlugins['flashMessenger']= $flashMessenger;

        $flashMessenger->expects($this->any())
            ->method('setNamespace')
            ->with('lmcuser-login-form')->willReturnSelf();

        $flashMessenger->expects($this->any())
            ->method('addMessage')->willReturnSelf();

        $postArray = array('some', 'data');
        $request = $this->createMock('Laminas\Http\Request');
        $request->expects($this->any())
            ->method('isPost')
            ->willReturn(true);
        $request->expects($this->any())
            ->method('getPost')
            ->willReturn($postArray);

        $this->helperMakePropertyAccessable($controller, 'request', $request);

        $form = $this->getMockBuilder('LmcUser\Form\Login')
            ->disableOriginalConstructor()
            ->getMock();

        $form->expects($this->any())
            ->method('isValid')
            ->willReturn((bool) $isValid);


        $this->options->expects($this->any())
            ->method('getUseRedirectParameterIfPresent')
            ->willReturn((bool) $wantRedirect);
        if ($wantRedirect) {
            $params = new Parameters();
            $params->set('redirect', $redirectUrl);

            $request->expects($this->any())
                ->method('getQuery')
                ->willReturn($params);
        }

        if ($isValid) {
            $adapter = $this->createMock('LmcUser\Authentication\Adapter\AdapterChain');
            $adapter->expects($this->once())
                ->method('resetAdapters');

            $service = $this->createMock('Laminas\Authentication\AuthenticationService');
            $service->expects($this->once())
                ->method('clearIdentity');

            $plugin = $this->setUpLmcUserAuthenticationPlugin(
                array(
                'getAuthAdapter'=>$adapter,
                'getAuthService'=>$service
                )
            );

            $form->expects($this->once())
                ->method('setData')
                ->with($postArray);

            $expectedResult = new \stdClass();

            $forwardPlugin = $this->getMockBuilder('Laminas\Mvc\Controller\Plugin\Forward')
                ->disableOriginalConstructor()
                ->getMock();
            $forwardPlugin->expects($this->once())
                ->method('dispatch')
                ->with($controller::CONTROLLER_NAME, array('action' => 'authenticate'))
                ->willReturn($expectedResult);

            $this->pluginManagerPlugins['forward']= $forwardPlugin;
        } else {
            $expectedResult = [
                'loginForm' => $form,
                'redirect' => $wantRedirect ? $redirectUrl : false,
                'enableRegistration' => false,
            ];
        }


        $controller->setLoginForm($form);
        $result = $controller->loginAction();

        if ($isValid) {
            $this->assertSame($expectedResult, $result);
        } else {
            $this->assertIsArray($result);
            $this->assertEquals($expectedResult, $result);
        }
    }

    /**
     * @dataProvider providerTrueOrFalse
     * @depend       testActionControllHasIdentity
     */
    public function testLoginActionIsNotPost($redirect)
    {
        $plugin = $this->setUpLmcUserAuthenticationPlugin(
            array(
            'hasIdentity'=>false
            )
        );

        $flashMessenger = $this->createMock('Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger');

        $this->pluginManagerPlugins['flashMessenger'] = $flashMessenger;

        $request = $this->createMock('Laminas\Http\Request');
        $request->expects($this->once())
            ->method('isPost')
            ->willReturn(false);

        $form = $this->getMockBuilder('LmcUser\Form\Login')
            ->disableOriginalConstructor()
            ->getMock();
        $form->expects($this->never())
            ->method('isValid');

        $this->options->expects($this->any())
            ->method('getUseRedirectParameterIfPresent')
            ->willReturn((bool) $redirect);
        if ($redirect) {
            $params = new Parameters();
            $params->set('redirect', 'http://localhost/');

            $request->expects($this->any())
                ->method('getQuery')
                ->willReturn($params);
        }

        $this->helperMakePropertyAccessable($this->controller, 'request', $request);

        $this->controller->setLoginForm($form);
        $result = $this->controller->loginAction();

        $this->assertArrayHasKey('loginForm', $result);
        $this->assertArrayHasKey('redirect', $result);
        $this->assertArrayHasKey('enableRegistration', $result);

        $this->assertInstanceOf('LmcUser\Form\Login', $result['loginForm']);
        $this->assertSame($form, $result['loginForm']);

        if ($redirect) {
            $this->assertEquals('http://localhost/', $result['redirect']);
        } else {
            $this->assertFalse($result['redirect']);
        }

        $this->assertEquals($this->options->getEnableRegistration(), $result['enableRegistration']);
    }


    /**
     * @dataProvider providerRedirectPostQueryMatrix
     * @depend       testActionControllHasIdentity
     */
    public function testLogoutAction($withRedirect, $post, $query)
    {
        $controller = $this->controller;

        $adapter = $this->createMock('LmcUser\Authentication\Adapter\AdapterChain');
        $adapter->expects($this->once())
            ->method('resetAdapters');

        $adapter->expects($this->once())
            ->method('logoutAdapters');

        $service = $this->createMock('Laminas\Authentication\AuthenticationService');
        $service->expects($this->once())
            ->method('clearIdentity');

        $this->setUpLmcUserAuthenticationPlugin(
            array(
            'getAuthAdapter'=>$adapter,
            'getAuthService'=>$service
            )
        );


        $response = new Response();

        $this->redirectCallback->expects($this->once())
            ->method('__invoke')
            ->willReturn($response);

        $result = $controller->logoutAction();

        $this->assertInstanceOf('Laminas\Http\Response', $result);
        $this->assertSame($response, $result);
    }


    /**
     * @dataProvider providerTestAuthenticateAction
     * @depend       testActionControllHasIdentity
     */
    public function testAuthenticateAction($wantRedirect, $post, $query, $prepareResult = false, $authValid = false)
    {
        $controller = $this->controller;
        $response = new Response();
        $hasRedirect = !(is_null($query) && is_null($post));

        $params = $this->createMock('Laminas\Mvc\Controller\Plugin\Params');
        $params->expects($this->any())
            ->method('__invoke')->willReturnSelf();
        $params->expects($this->once())
            ->method('fromPost')
            ->willReturnCallback(
                function ($key, $default) use ($post) {
                    return $post ?: $default;
                }
            );
        $params->expects($this->once())
            ->method('fromQuery')
            ->willReturnCallback(
                function ($key, $default) use ($query) {
                    return $query ?: $default;
                }
            );
        $this->pluginManagerPlugins['params'] = $params;


        $request = $this->createMock('Laminas\Http\Request');
        $this->helperMakePropertyAccessable($controller, 'request', $request);


        $adapter = $this->createMock('LmcUser\Authentication\Adapter\AdapterChain');
        $adapter->expects($this->once())
            ->method('prepareForAuthentication')
            ->with($request)
            ->willReturn($prepareResult);

        $service = $this->createMock('Laminas\Authentication\AuthenticationService');


        $this->setUpLmcUserAuthenticationPlugin(
            array(
            'hasIdentity'=>false,
            'getAuthAdapter'=>$adapter,
            'getAuthService'=>$service
            )
        );

        if (is_bool($prepareResult)) {
            $authResult = $this->getMockBuilder('Laminas\Authentication\Result')
                ->disableOriginalConstructor()
                ->getMock();
            $authResult->expects($this->once())
                ->method('isValid')
                ->willReturn($authValid);

            $service->expects($this->once())
                ->method('authenticate')
                ->with($adapter)
                ->willReturn($authResult);

            $redirect = $this->createMock('Laminas\Mvc\Controller\Plugin\Redirect');
            $this->pluginManagerPlugins['redirect'] = $redirect;

            if (!$authValid) {
                $flashMessenger = $this->createMock(
                    'Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger'
                );
                $this->pluginManagerPlugins['flashMessenger']= $flashMessenger;

                $flashMessenger->expects($this->once())
                    ->method('setNamespace')
                    ->with('lmcuser-login-form')->willReturnSelf();

                $flashMessenger->expects($this->once())
                    ->method('addMessage');

                $adapter->expects($this->once())
                    ->method('resetAdapters');

                $redirectQuery = ($post ?: $query ?: false);
                $redirectQuery = $redirectQuery ? '?redirect=' . rawurlencode($redirectQuery) : '';

                $redirect->expects($this->once())
                    ->method('toUrl')
                    ->with('user/login' . $redirectQuery)
                    ->willReturn($response);

                $url = $this->createMock('Laminas\Mvc\Controller\Plugin\Url');
                $url->expects($this->once())
                    ->method('fromRoute')
                    ->with($controller::ROUTE_LOGIN)
                    ->willReturn('user/login');
                $this->pluginManagerPlugins['url'] = $url;
            } else {
                $this->redirectCallback->expects($this->once())
                    ->method('__invoke');
            }

            $this->options->expects($this->any())
                ->method('getUseRedirectParameterIfPresent')
                ->willReturn((bool) $wantRedirect);
        }

        $result = $controller->authenticateAction();
    }

    /**
     *
     * @depend testActionControllHasIdentity
     */
    public function testRegisterActionIsNotAllowed()
    {
        $controller = $this->controller;

        $this->setUpLmcUserAuthenticationPlugin(
            array(
            'hasIdentity'=>false
            )
        );

        $this->options->expects($this->once())
            ->method('getEnableRegistration')
            ->willReturn(false);

        $result = $controller->registerAction();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('enableRegistration', $result);
        $this->assertFalse($result['enableRegistration']);
    }

    /**
     *
     * @dataProvider providerTestRegisterAction
     * @depend       testActionControllHasIdentity
     * @depend       testRegisterActionIsNotAllowed
     */
    public function testRegisterAction($wantRedirect, $postRedirectGetReturn, $registerSuccess, $loginAfterSuccessWith)
    {
        $controller = $this->controller;
        $redirectUrl = 'localhost/redirect1';
        $route_url = '/user/register';
        $expectedResult = null;

        $this->setUpLmcUserAuthenticationPlugin(
            array(
            'hasIdentity'=>false
            )
        );

        $this->options->expects($this->any())
            ->method('getEnableRegistration')
            ->willReturn(true);

        $request = $this->createMock('Laminas\Http\Request');
        $this->helperMakePropertyAccessable($controller, 'request', $request);

        $userService = $this->createMock('LmcUser\Service\User');
        $controller->setUserService($userService);

        $form = $this->getMockBuilder('Laminas\Form\Form')
            ->disableOriginalConstructor()
            ->getMock();

        $controller->setRegisterForm($form);

        $this->options->expects($this->any())
            ->method('getUseRedirectParameterIfPresent')
            ->willReturn((bool) $wantRedirect);

        if ($wantRedirect) {
            $params = new Parameters();
            $params->set('redirect', $redirectUrl);

            $request->expects($this->any())
                ->method('getQuery')
                ->willReturn($params);
        }


        $url = $this->createMock('Laminas\Mvc\Controller\Plugin\Url');
        $url->expects($this->atLeastOnce())
            ->method('fromRoute')
            ->withConsecutive([$controller::ROUTE_REGISTER], [$controller::ROUTE_LOGIN])
            ->willReturn('/user/register', '/user/login');

        $this->pluginManagerPlugins['url']= $url;

        $prg = $this->createMock('Laminas\Mvc\Plugin\Prg\PostRedirectGet');
        $this->pluginManagerPlugins['prg'] = $prg;

        $redirectQuery = $wantRedirect ? '?redirect=' . rawurlencode($redirectUrl) : '';
        $prg->expects($this->once())
            ->method('__invoke')
            ->with($route_url . $redirectQuery)
            ->willReturn($postRedirectGetReturn);

        if ($registerSuccess) {
            $user = new UserIdentity();
            $user->setEmail('lmc-user@trash-mail.com');
            $user->setUsername('lmc-user');

            $userService->expects($this->once())
                ->method('register')
                ->with($postRedirectGetReturn)
                ->willReturn($user);

            $userService->expects($this->any())
                ->method('getOptions')
                ->willReturn($this->options);

            $this->options->expects($this->once())
                ->method('getLoginAfterRegistration')
                ->willReturn(!empty($loginAfterSuccessWith));

            if ($loginAfterSuccessWith) {
                $this->options->expects($this->once())
                    ->method('getAuthIdentityFields')
                    ->willReturn(array($loginAfterSuccessWith));


                $expectedResult = new \stdClass();
                $forwardPlugin = $this->getMockBuilder('Laminas\Mvc\Controller\Plugin\Forward')
                    ->disableOriginalConstructor()
                    ->getMock();
                $forwardPlugin->expects($this->once())
                    ->method('dispatch')
                    ->with($controller::CONTROLLER_NAME, array('action' => 'authenticate'))
                    ->willReturn($expectedResult);

                $this->pluginManagerPlugins['forward']= $forwardPlugin;
            } else {
                $response = new Response();
                $route_url = '/user/login';

                $redirectUrl = isset($postRedirectGetReturn['redirect'])
                    ? $postRedirectGetReturn['redirect']
                    : null;

                $redirectQuery = $redirectUrl ? '?redirect='. rawurlencode($redirectUrl) : '';

                $redirect = $this->createMock('Laminas\Mvc\Controller\Plugin\Redirect');
                $redirect->expects($this->once())
                    ->method('toUrl')
                    ->with($route_url . $redirectQuery)
                    ->willReturn($response);

                $this->pluginManagerPlugins['redirect']= $redirect;


//                $url->expects($this->atLeastOnce())
//                    ->method('fromRoute')
//                    ->with($controller::ROUTE_LOGIN)
//                    ->willReturn($route_url);
            }
        }

        /***********************************************
         * run
         */
        $result = $controller->registerAction();

        /***********************************************
         * assert
         */
        if ($postRedirectGetReturn instanceof Response) {
            $expectedResult = $postRedirectGetReturn;
        }
        if ($expectedResult) {
            $this->assertSame($expectedResult, $result);
            return;
        }

        if ($postRedirectGetReturn === false) {
            $expectedResult = array(
                'registerForm' => $form,
                'enableRegistration' => true,
                'redirect' => $wantRedirect ? $redirectUrl : false
            );
        } elseif ($registerSuccess === false) {
            $expectedResult = array(
                'registerForm' => $form,
                'enableRegistration' => true,
                'redirect' => isset($postRedirectGetReturn['redirect']) ? $postRedirectGetReturn['redirect'] : null
            );
        }

        if ($expectedResult) {
            $this->assertIsArray($result);
            $this->assertArrayHasKey('registerForm', $result);
            $this->assertArrayHasKey('enableRegistration', $result);
            $this->assertArrayHasKey('redirect', $result);
            $this->assertEquals($expectedResult, $result);
        } else {
            $this->assertInstanceOf('Laminas\Http\Response', $result);
            $this->assertSame($response, $result);
        }
    }


    /**
     * @dataProvider providerTestChangeAction
     * @depend       testActionControllHasIdentity
     */
    public function testChangepasswordAction($status, $postRedirectGetReturn, $isValid, $changeSuccess)
    {
        $controller = $this->controller;
        $response = new Response();

        $this->setUpLmcUserAuthenticationPlugin(
            array(
            'hasIdentity'=>true
            )
        );

        $form = $this->getMockBuilder('Laminas\Form\Form')
            ->disableOriginalConstructor()
            ->getMock();


        $controller->setChangePasswordForm($form);


        $flashMessenger = $this->createMock(
            'Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger'
        );
        $this->pluginManagerPlugins['flashMessenger']= $flashMessenger;

        $flashMessenger->expects($this->any())
            ->method('setNamespace')
            ->with('change-password')->willReturnSelf();

        $flashMessenger->expects($this->once())
            ->method('getMessages')
            ->willReturn($status ? array('test') : array());


        $prg = $this->createMock('Laminas\Mvc\Plugin\Prg\PostRedirectGet');
        $this->pluginManagerPlugins['prg'] = $prg;


        $prg->expects($this->once())
            ->method('__invoke')
            ->with($controller::ROUTE_CHANGEPASSWD)
            ->willReturn($postRedirectGetReturn);

        if ($postRedirectGetReturn !== false && !($postRedirectGetReturn instanceof Response)) {
            $form->expects($this->once())
                ->method('setData')
                ->with($postRedirectGetReturn);

            $form->expects($this->once())
                ->method('isValid')
                ->willReturn((bool) $isValid);

            if ($isValid) {
                $userService = $this->createMock('LmcUser\Service\User');

                $controller->setUserService($userService);

                $form->expects($this->once())
                    ->method('getData')
                    ->willReturn($postRedirectGetReturn);

                $userService->expects($this->once())
                    ->method('changePassword')
                    ->with($postRedirectGetReturn)
                    ->willReturn((bool) $changeSuccess);


                if ($changeSuccess) {
                    $flashMessenger->expects($this->once())
                        ->method('addMessage')
                        ->with(true);


                    $redirect = $this->createMock('Laminas\Mvc\Controller\Plugin\Redirect');
                    $redirect->expects($this->once())
                        ->method('toRoute')
                        ->with($controller::ROUTE_CHANGEPASSWD)
                        ->willReturn($response);

                    $this->pluginManagerPlugins['redirect']= $redirect;
                }
            }
        }


        $result = $controller->changepasswordAction();
        $exceptedReturn = null;

        if ($postRedirectGetReturn instanceof Response) {
            $this->assertInstanceOf('Laminas\Http\Response', $result);
            $this->assertSame($postRedirectGetReturn, $result);
        } else {
            if ($postRedirectGetReturn === false) {
                $exceptedReturn = array(
                    'status' => $status ? 'test' : null,
                    'changePasswordForm' => $form,
                );
            } elseif ($isValid === false || $changeSuccess === false) {
                $exceptedReturn = array(
                    'status' => false,
                    'changePasswordForm' => $form,
                );
            }
            if ($exceptedReturn) {
                $this->assertIsArray($result);
                $this->assertArrayHasKey('status', $result);
                $this->assertArrayHasKey('changePasswordForm', $result);
                $this->assertEquals($exceptedReturn, $result);
            } else {
                $this->assertInstanceOf('Laminas\Http\Response', $result);
                $this->assertSame($response, $result);
            }
        }
    }


    /**
     * @dataProvider providerTestChangeAction
     * @depend       testActionControllHasIdentity
     */
    public function testChangeEmailAction($status, $postRedirectGetReturn, $isValid, $changeSuccess)
    {
        $controller = $this->controller;
        $response = new Response();
        $userService = $this->createMock('LmcUser\Service\User');
        $authService = $this->createMock('Laminas\Authentication\AuthenticationService');
        $identity = new UserIdentity();

        $controller->setUserService($userService);

        $this->setUpLmcUserAuthenticationPlugin(
            array(
            'hasIdentity'=>true
            )
        );

        $form = $this->getMockBuilder('Laminas\Form\Form')
            ->disableOriginalConstructor()
            ->getMock();

        $controller->setChangeEmailForm($form);

        $userService->expects($this->once())
            ->method('getAuthService')
            ->willReturn($authService);

        $authService->expects($this->once())
            ->method('getIdentity')
            ->willReturn($identity);
        $identity->setEmail('user@example.com');


        $requestParams = $this->createMock('Laminas\Stdlib\Parameters');
        $requestParams->expects($this->once())
            ->method('set')
            ->with('identity', $identity->getEmail());

        $request = $this->createMock('Laminas\Http\Request');
        $request->expects($this->once())
            ->method('getPost')
            ->willReturn($requestParams);
        $this->helperMakePropertyAccessable($controller, 'request', $request);



        $flashMessenger = $this->createMock(
            'Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger'
        );
        $this->pluginManagerPlugins['flashMessenger']= $flashMessenger;

        $flashMessenger->expects($this->any())
            ->method('setNamespace')
            ->with('change-email')->willReturnSelf();

        $flashMessenger->expects($this->once())
            ->method('getMessages')
            ->willReturn($status ? array('test') : array());


        $prg = $this->createMock('Laminas\Mvc\Plugin\Prg\PostRedirectGet');
        $this->pluginManagerPlugins['prg'] = $prg;


        $prg->expects($this->once())
            ->method('__invoke')
            ->with($controller::ROUTE_CHANGEEMAIL)
            ->willReturn($postRedirectGetReturn);

        if ($postRedirectGetReturn !== false && !($postRedirectGetReturn instanceof Response)) {
            $form->expects($this->once())
                ->method('setData')
                ->with($postRedirectGetReturn);

            $form->expects($this->once())
                ->method('isValid')
                ->willReturn((bool) $isValid);

            if ($isValid) {
                $userService->expects($this->once())
                    ->method('changeEmail')
                    ->with($postRedirectGetReturn)
                    ->willReturn((bool) $changeSuccess);


                if ($changeSuccess) {
                    $flashMessenger->expects($this->once())
                        ->method('addMessage')
                        ->with(true);


                    $redirect = $this->createMock('Laminas\Mvc\Controller\Plugin\Redirect');
                    $redirect->expects($this->once())
                        ->method('toRoute')
                        ->with($controller::ROUTE_CHANGEEMAIL)
                        ->willReturn($response);

                    $this->pluginManagerPlugins['redirect']= $redirect;
                } else {
                    $flashMessenger->expects($this->once())
                        ->method('addMessage')
                        ->with(false);
                }
            }
        }


        $result = $controller->changeEmailAction();
        $exceptedReturn = null;

        if ($postRedirectGetReturn instanceof Response) {
            $this->assertInstanceOf('Laminas\Http\Response', $result);
            $this->assertSame($postRedirectGetReturn, $result);
        } else {
            if ($postRedirectGetReturn === false) {
                $exceptedReturn = array(
                    'status' => $status ? 'test' : null,
                    'changeEmailForm' => $form,
                );
            } elseif ($isValid === false || $changeSuccess === false) {
                $exceptedReturn = array(
                    'status' => false,
                    'changeEmailForm' => $form,
                );
            }

            if ($exceptedReturn) {
                $this->assertIsArray($result);
                $this->assertArrayHasKey('status', $result);
                $this->assertArrayHasKey('changeEmailForm', $result);
                $this->assertEquals($exceptedReturn, $result);
            } else {
                $this->assertInstanceOf('Laminas\Http\Response', $result);
                $this->assertSame($response, $result);
            }
        }
    }

    /**
     * @dataProvider providerTestSetterGetterServices
     * @depend       testActionControllHasIdentity
     */
    public function testSetterGetterServices(
        $method,
        $useServiceLocator,
        $servicePrototype,
        $serviceName,
        $callback = null
    ) {
        $controller = new Controller($this->redirectCallback);
        $controller->setPluginManager($this->pluginManager);

        if (is_callable($callback)) {
            call_user_func($callback, $this, $controller);
        }

        if ($useServiceLocator) {
            $serviceLocator = $this->createMock('Laminas\ServiceManager\ServiceLocatorInterface');
            $serviceLocator->expects($this->once())
                ->method('get')
                ->with($serviceName)
                ->willReturn($servicePrototype);
            $controller->setServiceLocator($serviceLocator);
        } else {
            call_user_func(array($controller, 'set' . $method), $servicePrototype);
        }

        $result = call_user_func(array($controller, 'get' . $method));
        $this->assertInstanceOf(get_class($servicePrototype), $result);
        $this->assertSame($servicePrototype, $result);

        // we need two check for every case
        $result = call_user_func(array($controller, 'get' . $method));
        $this->assertInstanceOf(get_class($servicePrototype), $result);
        $this->assertSame($servicePrototype, $result);
    }

    public function providerTrueOrFalse()
    {
        return array(
            array(true),
            array(false),
        );
    }

    public function providerTrueOrFalseX2()
    {
        return array(
            array(true,true),
            array(true,false),
            array(false,true),
            array(false,false),
        );
    }

    public function providerTestAuthenticateAction()
    {
        // $redirect, $post, $query, $prepareResult = false, $authValid = false
        return array(
            array(false, null,              null,              new Response(), false),
            array(false, null,              null,              false,          false),
            array(false, null,              null,              false,          true),
            array(false, 'localhost/test1', null,              false,          false),
            array(false, 'localhost/test1', null,              false,          true),
            array(false, 'localhost/test1', 'localhost/test2', false,          false),
            array(false, 'localhost/test1', 'localhost/test2', false,          true),
            array(false, null,              'localhost/test2', false,          false),
            array(false, null,              'localhost/test2', false,          true),

            array(true,  null,              null,              false,          false),
            array(true,  null,              null,              false,          true),
            array(true,  'localhost/test1', null,              false,          false),
            array(true,  'localhost/test1', null,              false,          true),
            array(true,  'localhost/test1', 'localhost/test2', false,          false),
            array(true,  'localhost/test1', 'localhost/test2', false,          true),
            array(true,  null,              'localhost/test2', false,          false),
            array(true,  null,              'localhost/test2', false,          true),
        );
    }

    public function providerRedirectPostQueryMatrix()
    {
        return array(
            array(false, false, false),
            array(true, false, false),
            array(true, 'localhost/test1', false),
            array(true, 'localhost/test1', 'localhost/test2'),
            array(true, false,              'localhost/test2'),
        );
    }

    public function providerTestSetterGetterServices()
    {
        $that = $this;
        $loginFormCallback[] = function ($that, $controller) {
            $flashMessenger = $that->createMock(
                'Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger'
            );
            $that->pluginManagerPlugins['flashMessenger']= $flashMessenger;

            $flashMessenger->expects($that->any())
                ->method('setNamespace')
                ->with('lmcuser-login-form')->willReturnSelf();
        };
        $loginFormCallback[] = function ($that, $controller) {
            $flashMessenger = $that->createMock(
                'Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger'
            );
            $that->pluginManagerPlugins['flashMessenger']= $flashMessenger;

            $flashMessenger->expects($that->any())
                ->method('setNamespace')
                ->with('lmcuser-login-form')->willReturnSelf();
        };



        return array(
            // $method, $useServiceLocator, $servicePrototype, $serviceName, $loginFormCallback
            array('UserService', true, new UserService(), 'lmcuser_user_service' ),
            array('UserService', false, new UserService(), null ),
            array('RegisterForm', true, new Form(), 'lmcuser_register_form' ),
            array('RegisterForm', false, new Form(), null ),
            array('ChangePasswordForm', true, new Form(), 'lmcuser_change_password_form' ),
            array('ChangePasswordForm', false, new Form(), null ),
            array('ChangeEmailForm', true, new Form(), 'lmcuser_change_email_form' ),
            array('ChangeEmailForm', false, new Form(), null ),
            array('LoginForm', true, new Form(), 'lmcuser_login_form', $loginFormCallback[0] ),
            array('LoginForm', true, new Form(), 'lmcuser_login_form', $loginFormCallback[1] ),
            array('LoginForm', false, new Form(), null, $loginFormCallback[0] ),
            array('LoginForm', false, new Form(), null, $loginFormCallback[1] ),
            array('Options', true, new ModuleOptions(), 'lmcuser_module_options' ),
            array('Options', false, new ModuleOptions(), null ),
        );
    }


    public function providerTestActionControllHasIdentity()
    {

        return array(
            //    $methodeName , $hasIdentity, $redirectRoute,           optionsGetterMethode
            array('indexAction',          false, Controller::ROUTE_LOGIN,  null),
            array('loginAction',          true,  'user/overview',          'getLoginRedirectRoute'),
            array('authenticateAction',   true,  'user/overview',          'getLoginRedirectRoute'),
            array('registerAction',       true,  'user/overview',          'getLoginRedirectRoute'),
            array('changepasswordAction', false, 'user/overview',          'getLoginRedirectRoute'),
            array('changeEmailAction',    false, 'user/overview',          'getLoginRedirectRoute')

        );
    }


    public function providerTestChangeAction()
    {
        return array(
            //    $status, $postRedirectGetReturn, $isValid, $changeSuccess
            array(false,   new Response(),  null,  null),
            array(true,    new Response(),  null,  null),

            array(false,   false,           null,  null),
            array(true,    false,           null,  null),

            array(false,   array("test"),   false,  null),
            array(true,    array("test"),   false,  null),

            array(false,   array("test"),   true, false),
            array(true,    array("test"),   true, false),

            array(false,   array("test"),   true, true),
            array(true,    array("test"),   true, true),

        );
    }


    public function providerTestRegisterAction()
    {
        $registerPost = array (
            'username'=>'lmc-user',
            'email'=>'lmc-user@trash-mail.com',
            'password'=>'secret'
        );
        $registerPostRedirect = array_merge($registerPost, array("redirect" => 'test'));


        return array(
            //    $status, $postRedirectGetReturn, $registerSuccess, $loginAfterSuccessWith
            array(false,   new Response(),  null,  null),
            array(true,    new Response(),  null,  null),

            array(false,   false,           null,  null),
            array(true,    false,           null,  null),

            array(false,   $registerPost,   false,  null),
            array(true,    $registerPost,   false,  null),
            array(false,   $registerPostRedirect,   false,  null),
            array(true,    $registerPostRedirect,   false,  null),

            array(false,   $registerPost,   true, 'email'),
            array(true,    $registerPost,   true, 'email'),
            array(false,   $registerPostRedirect,   true, 'email'),
            array(true,    $registerPostRedirect,   true, 'email'),

            array(false,   $registerPost,   true, 'username'),
            array(true,    $registerPost,   true, 'username'),
            array(false,   $registerPostRedirect,   true, 'username'),
            array(true,    $registerPostRedirect,   true, 'username'),

            array(false,   $registerPost,   true, null),
            array(true,    $registerPost,   true, null),
            array(false,   $registerPostRedirect,   true, null),
            array(true,    $registerPostRedirect,   true, null),

        );
    }


    /**
     *
     * @param  mixed  $objectOrClass
     * @param  string $property
     * @param  mixed  $value         = null
     * @return \ReflectionProperty
     */
    public function helperMakePropertyAccessable($objectOrClass, $property, $value = null)
    {
        $reflectionProperty = new \ReflectionProperty($objectOrClass, $property);
        $reflectionProperty->setAccessible(true);

        if ($value !== null) {
            $reflectionProperty->setValue($objectOrClass, $value);
        }
        return $reflectionProperty;
    }

    public function helperMockCallbackPluginManagerGet($key)
    {
        if ($key=="flashMessenger" && !array_key_exists($key, $this->pluginManagerPlugins)) {
            //             echo "\n\n";
            //             echo '$key: ' . $key . "\n";
            //             var_dump(array_key_exists($key, $this->pluginManagerPlugins), array_keys($this->pluginManagerPlugins));
            //             exit;
        }
        return (array_key_exists($key, $this->pluginManagerPlugins))
            ? $this->pluginManagerPlugins[$key]
            : null;
    }
}
