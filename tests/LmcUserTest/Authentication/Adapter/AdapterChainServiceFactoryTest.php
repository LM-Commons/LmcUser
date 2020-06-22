<?php

namespace LmcUserTest\Authentication\Adapter;

use LmcUser\Authentication\Adapter\AdapterChainServiceFactory;

class AdapterChainServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The object to be tested.
     *
     * @var AdapterChainServiceFactory
     */
    protected $factory;

    /**
     * @var \Laminas\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var \LmcUser\Options\ModuleOptions
     */
    protected $options;

    /**
     * @var \Laminas\EventManager\EventManagerInterface
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
        $this->serviceLocator = $this->getMock('Laminas\ServiceManager\ServiceLocatorInterface');

        $this->options = $this->getMockBuilder('LmcUser\Options\ModuleOptions')
            ->disableOriginalConstructor()
            ->getMock();

        $this->serviceLocatorArray = array (
            'lmcuser_module_options'=>$this->options
        );

        $this->serviceLocator->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(array($this,'helperServiceLocator')));

        $this->eventManager = $this->getMock('Laminas\EventManager\EventManager');

        $this->factory = new AdapterChainServiceFactory();
    }

    /**
     * @covers \LmcUser\Authentication\Adapter\AdapterChainServiceFactory::createService
     */
    public function testCreateService()
    {
        $adapter = array(
            'adapter1'=> $this->getMock(
                'LmcUser\Authentication\Adapter\AbstractAdapter',
                array('authenticate', 'logout')
            ),
            'adapter2'=> $this->getMock(
                'LmcUser\Authentication\Adapter\AbstractAdapter',
                array('authenticate', 'logout')
            )
        );
        $adapterNames = array(100=>'adapter1', 200=>'adapter2');

        $this->serviceLocatorArray = array_merge($this->serviceLocatorArray, $adapter);

        $this->options->expects($this->once())
                      ->method('getAuthAdapters')
                      ->will($this->returnValue($adapterNames));

        $adapterChain = $this->factory->__invoke($this->serviceLocator, 'LmcUser\Authentication\Adapter\AdapterChain');

        $this->assertInstanceOf('LmcUser\Authentication\Adapter\AdapterChain', $adapterChain);
    }

    /**
     * @covers \LmcUser\Authentication\Adapter\AdapterChainServiceFactory::setOptions
     * @covers \LmcUser\Authentication\Adapter\AdapterChainServiceFactory::getOptions
     */
    public function testGetOptionWithSetter()
    {
        $this->factory->setOptions($this->options);

        $options = $this->factory->getOptions();

        $this->assertInstanceOf('LmcUser\Options\ModuleOptions', $options);
        $this->assertSame($this->options, $options);


        $options2 = clone $this->options;
        $this->factory->setOptions($options2);
        $options = $this->factory->getOptions();

        $this->assertInstanceOf('LmcUser\Options\ModuleOptions', $options);
        $this->assertNotSame($this->options, $options);
        $this->assertSame($options2, $options);
    }

    /**
     * @covers \LmcUser\Authentication\Adapter\AdapterChainServiceFactory::getOptions
     */
    public function testGetOptionWithLocator()
    {
        $options = $this->factory->getOptions($this->serviceLocator);

        $this->assertInstanceOf('LmcUser\Options\ModuleOptions', $options);
        $this->assertSame($this->options, $options);
    }

    /**
     * @covers \LmcUser\Authentication\Adapter\AdapterChainServiceFactory::getOptions
     * @expectedException \LmcUser\Authentication\Adapter\Exception\OptionsNotFoundException
     */
    public function testGetOptionFailing()
    {
        $options = $this->factory->getOptions();
    }
}
