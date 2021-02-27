<?php
namespace LmcUserTest\Factory\Form;

use Laminas\Db\Adapter\Adapter;
use Laminas\ServiceManager\ServiceManager;
use LmcUser\Factory\Mapper\User as UserMapperFactory;
use LmcUser\Mapper\User as UserMapper;
use LmcUser\Mapper\UserHydrator;
use LmcUser\Options\ModuleOptions;
use LmcUser\Entity\User as UserEntity;
use PHPUnit\Framework\TestCase;

class UserMapperFactoryTest extends TestCase
{
    /**
     * @covers LmcUser\Factory\Mapper\User::__invoke
     */
    public function testFactory(): void
    {
        $moduleOptions = $this->createMock(ModuleOptions::class);
        $moduleOptions->expects($this->once())
            ->method('getUserEntityClass')
            ->willReturn(UserEntity::class);
        $moduleOptions->expects($this->once())
            ->method('getTableName')
            ->willReturn('user');

        $adapter = $this->createMock(Adapter::class);
        $hydrator = $this->createMock(UserHydrator::class);

        $serviceManager = new ServiceManager;
        $serviceManager->setService('lmcuser_module_options', $moduleOptions);
        $serviceManager->setService('lmcuser_laminas_db_adapter', $adapter);
        $serviceManager->setService('lmcuser_user_hydrator', $hydrator);

        $factory = new UserMapperFactory();

        $this->assertInstanceOf(UserMapper::class, $factory->__invoke($serviceManager, UserMapper::class));
    }
}
