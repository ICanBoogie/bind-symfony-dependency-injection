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
        $this->proxy ??= ServiceProvider::defined();
    }

    public function testGetContainer(): void
    {
        $this->assertInstanceOf(Container::class, $this->proxy->container);
    }

    public function testInvoke(): void
    {
        $proxy = $this->proxy;
        $this->assertInstanceOf(Application::class, $proxy->get('app'));
        $this->assertInstanceOf(Application::class, $proxy->get(Application::class));
    }

    public function testServices(): void
    {
        $proxy = $this->proxy;
        $this->assertInstanceOf(ServiceA::class, $proxy->get('service_a'));
        $this->assertInstanceOf(ServiceB::class, $proxy->get('service_b'));
        $this->assertInstanceOf(ServiceC::class, $proxy->get('service_c'));
    }
}
