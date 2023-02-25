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

use ICanBoogie\Application;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

use function ICanBoogie\app;

/**
 * @group integration
 */
final class ContainerProxyTest extends TestCase
{
    private ContainerInterface $container;

    protected function setUp(): void
    {
        $this->container = app()->container;
    }

    public function testGetContainer(): void
    {
        $container = $this->container;
        // Make sure synthetic service is defined.
        $this->assertInstanceOf(Application::class, $app = $container->get(Application::class));
        $this->assertSame($app, $container->get('app'));
    }

    public function testServices(): void
    {
        $this->assertInstanceOf(ServiceA::class, $this->container->get('service_a'));
        $this->assertInstanceOf(ServiceB::class, $this->container->get('service_b'));
        $this->assertInstanceOf(ServiceC::class, $this->container->get('service_c'));
    }
}
