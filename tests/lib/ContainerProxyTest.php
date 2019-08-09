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
	/**
	 * @var ContainerProxy
	 */
	private $proxy;

	protected function setUp(): void
	{
		$proxy = &$this->proxy;

		if (!$proxy)
		{
			$proxy = ServiceProvider::defined();

			$this->assertInstanceOf(ContainerProxy::class, $proxy);
		}
	}

	public function testGetContainer()
	{
		$this->assertInstanceOf(Container::class, $this->proxy->container);
	}

	public function testInvoke()
	{
		$proxy = $this->proxy;
		$this->assertInstanceOf(Application::class, $proxy('app'));
	}

	public function testServices()
	{
		$proxy = $this->proxy;
		$this->assertInstanceOf(ServiceA::class, $proxy('service_a'));
		$this->assertInstanceOf(ServiceB::class, $proxy('service_b'));
		$this->assertInstanceOf(ServiceC::class, $proxy('service_c'));
	}
}
