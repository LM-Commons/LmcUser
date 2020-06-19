<?php

namespace LaminasUserTest\Authentication\Adapter;

use LaminasUser\Authentication\Adapter\AdapterChainServiceFactory;

class AdapterChainServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The object to be tested.
     *
     * @var AdapterChainServiceFactory
     */
    protected $factory;

    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var \LaminasUser\Options\ModuleOptions
     */
    protected $options;

    /**
     * @var \Zend\EventManager\EventManagerInterface
     */
    protected $eventManager;


    protected $serviceLocatorArray;

    public function helperServiceLocator($index)
    {
        return $this->serviceLocatorArray[$index];
    }

    /**
     * Prepare the object to be tested.
     */
    protected function setUp()
    {
        $this->serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');

        $this->options = $this->getMockBuilder('LaminasUser\Options\ModuleOptions')
            ->disableOriginalConstructor()
            ->getMock();

        $this->serviceLocatorArray = array (
            'zfcuser_module_options'=>$this->options
        );

        $this->serviceLocator->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(array($this,'helperServiceLocator')));

        $this->eventManager = $this->getMock('Zend\EventManager\EventManager');

        $this->factory = new AdapterChainServiceFactory();
    }

    /**
     * @covers \LaminasUser\Authentication\Adapter\AdapterChainServiceFactory::createService
     */
    public function testCreateService()
    {
        $adapter = array(
            'adapter1'=> $this->getMock(
                'LaminasUser\Authentication\Adapter\AbstractAdapter',
                array('authenticate', 'logout')
            ),
            'adapter2'=> $this->getMock(
                'LaminasUser\Authentication\Adapter\AbstractAdapter',
                array('authenticate', 'logout')
            )
        );
        $adapterNames = array(100=>'adapter1', 200=>'adapter2');

        $this->serviceLocatorArray = array_merge($this->serviceLocatorArray, $adapter);

        $this->options->expects($this->once())
                      ->method('getAuthAdapters')
                      ->will($this->returnValue($adapterNames));

        $adapterChain = $this->factory->__invoke($this->serviceLocator, 'LaminasUser\Authentication\Adapter\AdapterChain');

        $this->assertInstanceOf('LaminasUser\Authentication\Adapter\AdapterChain', $adapterChain);
    }

    /**
     * @covers \LaminasUser\Authentication\Adapter\AdapterChainServiceFactory::setOptions
     * @covers \LaminasUser\Authentication\Adapter\AdapterChainServiceFactory::getOptions
     */
    public function testGetOptionWithSetter()
    {
        $this->factory->setOptions($this->options);

        $options = $this->factory->getOptions();

        $this->assertInstanceOf('LaminasUser\Options\ModuleOptions', $options);
        $this->assertSame($this->options, $options);


        $options2 = clone $this->options;
        $this->factory->setOptions($options2);
        $options = $this->factory->getOptions();

        $this->assertInstanceOf('LaminasUser\Options\ModuleOptions', $options);
        $this->assertNotSame($this->options, $options);
        $this->assertSame($options2, $options);
    }

    /**
     * @covers \LaminasUser\Authentication\Adapter\AdapterChainServiceFactory::getOptions
     */
    public function testGetOptionWithLocator()
    {
        $options = $this->factory->getOptions($this->serviceLocator);

        $this->assertInstanceOf('LaminasUser\Options\ModuleOptions', $options);
        $this->assertSame($this->options, $options);
    }

    /**
     * @covers \LaminasUser\Authentication\Adapter\AdapterChainServiceFactory::getOptions
     * @expectedException \LaminasUser\Authentication\Adapter\Exception\OptionsNotFoundException
     */
    public function testGetOptionFailing()
    {
        $options = $this->factory->getOptions();
    }
}
