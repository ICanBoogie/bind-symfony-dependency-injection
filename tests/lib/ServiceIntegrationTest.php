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

use ICanBoogie\Storage\Storage;
use PHPUnit\Framework\TestCase;

use function ICanBoogie\app;

final class ServiceIntegrationTest extends TestCase
{
	/**
	 * @dataProvider provideService
	 */
	public function testService(string $service_id, string $service_class): void
	{
		$service = app()->$service_id;

		$this->assertInstanceOf($service_class, $service);
		$this->assertSame($service, app()->container->get("public.app.$service_id"));
	}

	public function provideService(): array
	{
		return [

			[ 'vars', Storage::class ],

		];
	}
}
