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

        ];
    }

    public function test_compiler_pass_parameter(): void
    {
        $this->assertEquals(
            "Hello world!",
            app()->container->getParameter('compiler_pass_parameter')
        );
    }
}
