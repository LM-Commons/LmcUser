<?php

namespace LmcUserTest\View\Helper;

use LmcUser\View\Helper\LmcUserLoginWidget as ViewHelper;
use Laminas\View\Model\ViewModel;
use PHPUnit\Framework\TestCase;

class LmcUserLoginWidgetTest extends TestCase
{
    protected $helper;

    protected $view;

    public function setUp():void
    {
        $this->helper = new ViewHelper;

        $view = $this->createMock('Laminas\View\Renderer\RendererInterface');
        $this->view = $view;

        $this->helper->setView($view);
    }

    public function providerTestInvokeWithRender()
    {
        $attr = array();
        $attr[] = array(
            array(
                'render' => true,
                'redirect' => 'lmcUser'
            ),
            array(
                'loginForm' => null,
                'redirect' => 'lmcUser'
            ),
        );
        $attr[] = array(
            array(
                'redirect' => 'lmcUser'
            ),
            array(
                'loginForm' => null,
                'redirect' => 'lmcUser'
            ),
        );
        $attr[] = array(
            array(
                'render' => true,
            ),
            array(
                'loginForm' => null,
                'redirect' => false
            ),
        );

        return $attr;
    }

    /**
     * @covers       LmcUser\View\Helper\LmcUserLoginWidget::__invoke
     * @dataProvider providerTestInvokeWithRender
     */
    public function testInvokeWithRender($option, $expect)
    {
        /**
         * @var $viewModel \Laminas\View\Model\ViewModels
         */
        $viewModel = null;

        $this->view->expects($this->at(0))
            ->method('render')
            ->will(
                $this->returnCallback(
                    function ($vm) use (&$viewModel) {
                        $viewModel = $vm;
                        return "test";
                    }
                )
            );

        $result = $this->helper->__invoke($option);

        $this->assertNotInstanceOf('Laminas\View\Model\ViewModel', $result);
        $this->assertIsString($result);


        $this->assertInstanceOf('Laminas\View\Model\ViewModel', $viewModel);
        foreach ($expect as $name => $value) {
            $this->assertEquals($value, $viewModel->getVariable($name, "testDefault"));
        }
    }

    /**
     * @covers LmcUser\View\Helper\LmcUserLoginWidget::__invoke
     */
    public function testInvokeWithoutRender()
    {
        $result = $this->helper->__invoke(
            array(
            'render' => false,
            'redirect' => 'lmcUser'
            )
        );

        $this->assertInstanceOf('Laminas\View\Model\ViewModel', $result);
        $this->assertEquals('lmcUser', $result->redirect);
    }

    /**
     * @covers LmcUser\View\Helper\LmcUserLoginWidget::setLoginForm
     * @covers LmcUser\View\Helper\LmcUserLoginWidget::getLoginForm
     */
    public function testSetGetLoginForm()
    {
        $loginForm = $this->getMockBuilder('LmcUser\Form\Login')->disableOriginalConstructor()->getMock();

        $this->helper->setLoginForm($loginForm);
        $this->assertInstanceOf('LmcUser\Form\Login', $this->helper->getLoginForm());
    }

    /**
     * @covers LmcUser\View\Helper\LmcUserLoginWidget::setViewTemplate
     */
    public function testSetViewTemplate()
    {
        $this->helper->setViewTemplate('lmcUser');

        $reflectionClass = new \ReflectionClass('LmcUser\View\Helper\LmcUserLoginWidget');
        $reflectionProperty = $reflectionClass->getProperty('viewTemplate');
        $reflectionProperty->setAccessible(true);

        $this->assertEquals('lmcUser', $reflectionProperty->getValue($this->helper));
    }
}
