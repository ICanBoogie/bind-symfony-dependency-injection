<?php

namespace Test\ICanBoogie\Binding\SymfonyDependencyInjection;

use ICanBoogie\Binding\SymfonyDependencyInjection\Config;
use PHPUnit\Framework\TestCase;
use Test\ICanBoogie\Binding\SymfonyDependencyInjection\Acme\FakeCompilerPass;
use Test\ICanBoogie\Binding\SymfonyDependencyInjection\Acme\FakeExtension;

final class ConfigTest extends TestCase
{
    public function test_export(): void
    {
        $config = new Config(
            compiler_passes: [
                FakeCompilerPass::class,
                FakeCompilerPass::class,
            ],
            extensions: [
                FakeExtension::class,
                FakeExtension::class,
            ],
            use_caching: true
        );

        $actual = SetStateHelper::export_import($config);

        $this->assertEquals($config, $actual);
    }
}
