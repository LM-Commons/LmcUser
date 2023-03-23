<?php

namespace LmcUser\Factory\Mapper;

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use LmcUser\Mapper;
use LmcUser\Options\ModuleOptions;

class User implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /**
         * @var ModuleOptions $options
         */
        $options = $container->get('lmcuser_module_options');
        $dbAdapter = $container->get('lmcuser_laminas_db_adapter');
        $hydrator = $container->get('lmcuser_user_hydrator');

        $entityClass = $options->getUserEntityClass();
        $tableName = $options->getTableName();

        $mapper = new Mapper\User();
        $mapper->setDbAdapter($dbAdapter);
        $mapper->setTableName($tableName);
        $mapper->setEntityPrototype(new $entityClass);
        $mapper->setHydrator($hydrator);

        return $mapper;
    }
}
