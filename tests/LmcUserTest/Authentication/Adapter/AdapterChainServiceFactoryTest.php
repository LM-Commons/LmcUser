<?php

namespace LmcUserTest\Authentication\Adapter;

use Laminas\EventManager\EventManager;
use LmcUser\Authentication\Adapter\AdapterChainServiceFactory;
use PHPUnit\Framework\TestCase;

class AdapterChainServiceFactoryTest extends TestCase
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
        if (!array_key_exists($index, $this->serviceLocatorArray)) {
            throw new \Exception('index '.$index.' does not exist in serviceLocatorArray');
        }
        return $this->serviceLocatorArray[$index];
    }

    /**
     * Prepare the object to be tested.
     */
    protected function setUp():void
    {
        $this->serviceLocator = $this->createMock('Laminas\ServiceManager\ServiceLocatorInterface');

        $this->options = $this->getMockBuilder('LmcUser\Options\ModuleOptions')
            ->disableOriginalConstructor()
            ->getMock();

        $this->serviceLocatorArray = array (
            'lmcuser_module_options'=>$this->options,
            'EventManager'=>$this->createMock('Laminas\EventManager\EventManager')
        );

        $this->serviceLocator->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(array($this,'helperServiceLocator')));

        $this->eventManager = $this->createMock('Laminas\EventManager\EventManager');

        $this->factory = new AdapterChainServiceFactory();
    }

    /**
     * @covers \LmcUser\Authentication\Adapter\AdapterChainServiceFactory::createService
     */
    public function testCreateService()
    {
        $adapter = array(
            'adapter1'=> $this->createMock(
                'LmcUser\Authentication\Adapter\AbstractAdapter',
                array('authenticate', 'logout')
            ),
            'adapter2'=> $this->createMock(
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
     */
    public function testGetOptionFailing()
    {
        $this->expectException(\LmcUser\Authentication\Adapter\Exception\OptionsNotFoundException::class);
        $options = $this->factory->getOptions();
    }
}
