<?php

namespace Test\ICanBoogie\Binding\SymfonyDependencyInjection;

use ICanBoogie\Autoconfig\Autoconfig;
use ICanBoogie\ConfigProvider;
use ICanBoogie\ServiceProvider;
use ICanBoogie\Storage\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Test\ICanBoogie\Binding\SymfonyDependencyInjection\Acme\ServiceA;
use Test\ICanBoogie\Binding\SymfonyDependencyInjection\Acme\ServiceB;
use Test\ICanBoogie\Binding\SymfonyDependencyInjection\Acme\ServiceC;

use function ICanBoogie\app;

final class IntegrationTest extends TestCase
{
    /**
     * @dataProvider provideService
     *
     * @param class-string $class
     */
    public function testService(string $id, string $class): void
    {
        $actual = app()->service_for_id($id, $class);

        $this->assertInstanceOf($class, $actual);
    }

    /**
     * @return array<array{ string, class-string }>
     */
    public static function provideService(): array
    {
        return [

            [ 'service_a', ServiceA::class ],
            [ 'service_b', ServiceB::class ],
            [ 'service_c', ServiceC::class ],
            [ 'test.app.vars', Storage::class ],
            [ 'test.config_provider', ConfigProvider::class ],
            [ 'test.service_provider', ServiceProvider::class ],
            [ 'test.autoconfig', Autoconfig::class ],

        ];
    }

    public function test_compiler_pass_parameter(): void
    {
        $this->assertEquals(
            "Hello world!",
            app()->container->getParameter('compiler_pass_parameter')
        );
    }

    #[DataProvider("provide_app_config_parameter")]
    public function test_app_config_parameter(string $param, mixed $expected): void
    {
        $actual = app()->container->getParameter($param);

        $this->assertEquals($expected, $actual);
    }

    /** @phpstan-ignore-next-line */
    public static function provide_app_config_parameter(): array
    {
        return [
            [ "app.config.var", "var/" ],
            [ "app.config.var_cache", "var/cache/" ],
            [ "app.config.var_cache_configs", "var/cache/configs/" ],
            [ "app.config.var_files", "var/files/" ],
            [ "app.config.var_lib", "var/lib/" ],
            [ "app.config.var_tmp", "var/tmp/" ],
            [ "test.cache_dir", "var/cache/test/" ],
        ];
    }
}
