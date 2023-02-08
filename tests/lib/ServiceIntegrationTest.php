<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\ICanBoogie\Binding\SymfonyDependencyInjection;

use ICanBoogie\ConfigProvider;
use ICanBoogie\ServiceProvider;
use ICanBoogie\Storage\Storage;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

use function ICanBoogie\app;

final class ServiceIntegrationTest extends TestCase
{
    /**
     * @dataProvider provideService
     *
     * @param class-string $service_class
     */
    public function testService(string $service_id, string $service_class): void
    {
        $actual = app()->service_for_id($service_id, $service_class);

        $this->assertInstanceOf($service_class, $actual);
    }

    // @phpstan-ignore-next-line
    public function provideService(): array
    {
        return [

            [ 'test.app.vars', Storage::class ],
            [ 'test.config_provider', ConfigProvider::class ],
            [  'test.service_provider',  ServiceProvider::class ],

        ];
    }

    public function set_compiler_pass_parameter(): void
    {
        $container = app()->service_for_id('service_container', ContainerInterface::class);

        $this->assertEquals(
            "Hello world!",
            $container->getParameter('compiler_pass_parameter')
        );
    }
}
