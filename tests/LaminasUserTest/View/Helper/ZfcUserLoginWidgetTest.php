<?php

namespace LaminasUserTest\View\Helper;

use LaminasUser\View\Helper\LaminasUserLoginWidget as ViewHelper;
use Laminas\View\Model\ViewModel;

class LaminasUserLoginWidgetTest extends \PHPUnit_Framework_TestCase
{
    protected $helper;

    protected $view;

    public function setUp()
    {
        $this->helper = new ViewHelper;

        $view = $this->getMock('Laminas\View\Renderer\RendererInterface');
        $this->view = $view;

        $this->helper->setView($view);
    }

    public function providerTestInvokeWithRender()
    {
        $attr = array();
        $attr[] = array(
            array(
                'render' => true,
                'redirect' => 'laminasUser'
            ),
            array(
                'loginForm' => null,
                'redirect' => 'laminasUser'
            ),
        );
        $attr[] = array(
            array(
                'redirect' => 'laminasUser'
            ),
            array(
                'loginForm' => null,
                'redirect' => 'laminasUser'
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
     * @covers LaminasUser\View\Helper\LaminasUserLoginWidget::__invoke
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
             ->will($this->returnCallback(function ($vm) use (&$viewModel) {
                 $viewModel = $vm;
                 return "test";
             }));

        $result = $this->helper->__invoke($option);

        $this->assertNotInstanceOf('Laminas\View\Model\ViewModel', $result);
        $this->assertInternalType('string', $result);


        $this->assertInstanceOf('Laminas\View\Model\ViewModel', $viewModel);
        foreach ($expect as $name => $value) {
            $this->assertEquals($value, $viewModel->getVariable($name, "testDefault"));
        }
    }

    /**
     * @covers LaminasUser\View\Helper\LaminasUserLoginWidget::__invoke
     */
    public function testInvokeWithoutRender()
    {
        $result = $this->helper->__invoke(array(
            'render' => false,
            'redirect' => 'laminasUser'
        ));

        $this->assertInstanceOf('Laminas\View\Model\ViewModel', $result);
        $this->assertEquals('laminasUser', $result->redirect);
    }

    /**
     * @covers LaminasUser\View\Helper\LaminasUserLoginWidget::setLoginForm
     * @covers LaminasUser\View\Helper\LaminasUserLoginWidget::getLoginForm
     */
    public function testSetGetLoginForm()
    {
        $loginForm = $this->getMockBuilder('LaminasUser\Form\Login')->disableOriginalConstructor()->getMock();

        $this->helper->setLoginForm($loginForm);
        $this->assertInstanceOf('LaminasUser\Form\Login', $this->helper->getLoginForm());
    }

    /**
     * @covers LaminasUser\View\Helper\LaminasUserLoginWidget::setViewTemplate
     */
    public function testSetViewTemplate()
    {
        $this->helper->setViewTemplate('laminasUser');

        $reflectionClass = new \ReflectionClass('LaminasUser\View\Helper\LaminasUserLoginWidget');
        $reflectionProperty = $reflectionClass->getProperty('viewTemplate');
        $reflectionProperty->setAccessible(true);

        $this->assertEquals('laminasUser', $reflectionProperty->getValue($this->helper));
    }
}
