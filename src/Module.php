<?php

declare(strict_types=1);

namespace LmcUser;

class Module
{
    public const LMC_USER_SESSION_STORAGE_NAMESPACE = 'LmcUserNamespace';

    public function getConfig(): array
    {
        $configProvider = new ConfigProvider();
        return [
            'dependencies'       => $configProvider->getDependencies(),
            'view_helpers'       => $configProvider->getViewHelperConfig(),
            'view_manager'       => $configProvider->getViewManagerConfig(),
            'router'             => $configProvider->getRouterConfig(),
            'controllers'        => $configProvider->getControllerConfig(),
            'controller_plugins' => $configProvider->getControllerPluginConfig(),
        ];
    }
}
