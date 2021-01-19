<?php

namespace LmcUser\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

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
        return new \LmcUser\Mapper\UserHydrator($container->get('lmcuser_base_hydrator'));
    }
}
