<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie\Binding\SymfonyDependencyInjection;

use ICanBoogie\Application;
use ICanBoogie\Service\ServiceProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;

/**
 * @group integration
 */
final class ContainerProxyTest extends TestCase
{
    private ContainerProxy $proxy;

    protected function setUp(): void
    {
        $this->proxy ??= ServiceProvider::defined(); // @phpstan-ignore-line
    }

    public function testGetContainer(): void
    {
        $container = $this->proxy->container;
        $this->assertInstanceOf(Container::class, $container);
        // Make sure synthetic service is defined.
        $this->assertInstanceOf(Application::class, $app = $container->get(Application::class));
        $this->assertSame($app, $container->get('app'));
    }

    public function testInvoke(): void
    {
        $proxy = $this->proxy;
        $this->assertInstanceOf(Application::class, $app = $proxy->get(Application::class));
        $this->assertSame($app, $proxy->get('app'));
    }

    public function testServices(): void
    {
        $proxy = $this->proxy;
        $this->assertInstanceOf(ServiceA::class, $proxy->get('service_a'));
        $this->assertInstanceOf(ServiceB::class, $proxy->get('service_b'));
        $this->assertInstanceOf(ServiceC::class, $proxy->get('service_c'));
    }
}
