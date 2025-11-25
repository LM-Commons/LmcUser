<?php

declare(strict_types=1);

namespace LmcUserTest;

use LmcUser\ConfigProvider;
use PHPUnit\Framework\TestCase;

/** @covers \LmcUser\ConfigProvider */
class ConfigProviderTest extends TestCase
{
    public function testInvoke(): void
    {
        $configProvider = new ConfigProvider();
        $this->assertIsArray($configProvider());
    }

    public function testGetRouterConfig(): void
    {
        $configProvider = new ConfigProvider();
        $this->assertIsArray($configProvider->getRouterConfig());
    }

    public function testGetControllerConfig(): void
    {
        $configProvider = new ConfigProvider();
        $this->assertIsArray($configProvider->getControllerConfig());
    }

    public function testGetControllerPluginConfig(): void
    {
        $configProvider = new ConfigProvider();
        $this->assertIsArray($configProvider->getControllerPluginConfig());
    }

    public function testGetServiceConfig(): void
    {
        $configProvider = new ConfigProvider();
        $this->assertIsArray($configProvider->getServiceConfig());
    }
}
