<?php

namespace LmcUser\Factory;

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use LmcUser\Mapper\UserHydrator as UserHyDratorObject;

/**
 * Class UserHydrator
 */
class UserHydrator implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new UserHyDratorObject($container->get('lmcuser_base_hydrator'));
    }
}
