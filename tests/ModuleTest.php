<?php

declare(strict_types=1);

namespace LmcUserTest;

use LmcUser\Module;
use PHPUnit\Framework\TestCase;

/** @covers \LmcUser\Module */
class ModuleTest extends TestCase
{
    private Module $module;

    protected function setUp(): void
    {
        $this->module = new Module();
    }

    protected function tearDown(): void
    {
        unset($this->module);
    }

    /**
     * @covers \LmcUser\Module::getConfig
     */
    public function testGetConfig(): void
    {
        $this->assertIsArray($this->module->getConfig());
    }
}
