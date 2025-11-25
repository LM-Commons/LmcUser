<?php

declare(strict_types=1);

namespace LmcUser\Service;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class UserServiceFactory implements FactoryInterface
{

    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): User
    {
        return new User(
            $container->get('lmcuser_user_mapper'),
            $container->get('lmcuser_auth_service'),
            $container->get('lmcuser_login_form'),
            $container->get('lmcuser_register_form'),
            $container->get('lmcuser_change_password_form'),
            $container->get('lmcuser_module_options'),
            $container->get('lmcuser_register_form_hydrator')
        );
    }
}
