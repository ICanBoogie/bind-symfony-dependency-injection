<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\ICanBoogie\Binding\SymfonyDependencyInjection\Extension;

use ICanBoogie\Binding\SymfonyDependencyInjection\Extension\ApplicationExtension;
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
     * @dataProvider provideParameter
     */
    public function testParameter(string $param): void
    {
        $this->assertTrue($this->container->hasParameter($param));
    }

    /**
     * @return array<string[]>
     */
    public static function provideParameter(): array
    {
        return self::build_test_cases(
            <<<EOT
            app.app_path
            app.app_paths
            app.base_path
            app.autoconfig_filters
            app.config_constructor
            app.config_path
            app.locale_path
            EOT
        );
    }

    // @phpstan-ignore-next-line
    private static function build_test_cases(string $string): array
    {
        return array_map(function ($param) {
            return [ $param ];
        }, explode("\n", $string));
    }
}
