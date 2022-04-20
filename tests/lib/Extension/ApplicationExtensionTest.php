<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie\Binding\SymfonyDependencyInjection\Extension;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use function array_map;
use function explode;
use function ICanBoogie\app;

/**
 * @group integration
 */
final class ApplicationExtensionTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        $this->container ??= (function (): Container {
            $container = new ContainerBuilder();
            $extension = ApplicationExtension::from(app());
            $extension->load([], $container);

            return $container;
        })();
    }

    /**
     * @dataProvider provide_service
     */
    public function test_service(string $id): void
    {
        $container = $this->container;
        $this->assertTrue($container->has($id));
    }

    // @phpstan-ignore-next-line
    public function provide_service(): array
    {
        return $this->build_test_cases(
            <<<EOT
app
container
events
initial_request
logger
request
routes
session
EOT
        );
    }

    /**
     * @dataProvider provideParameter
     */
    public function testParameter(string $param): void
    {
        $this->assertTrue($this->container->hasParameter($param));
    }

    // @phpstan-ignore-next-line
    public function provideParameter(): array
    {
        return $this->build_test_cases(
            <<<EOT
app.app_path
app.app_paths
app.base_path
app.autoconfig_filters
app.cache.catalogs
app.cache.configs
app.cache.modules
app.config_constructor
app.config_path
app.error_handler
app.locale_path
app.repository
app.repository.cache
app.repository.cache.configs
app.repository.files
app.repository.tmp
app.repository.var
app.session
app.storage_for_configs
app.storage_for_vars
EOT
        );
    }

    // @phpstan-ignore-next-line
    private function build_test_cases(string $string): array
    {
        return array_map(function ($param) {
            return [ $param ];
        }, explode("\n", $string));
    }
}
