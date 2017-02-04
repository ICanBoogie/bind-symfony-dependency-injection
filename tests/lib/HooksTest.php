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

use ICanBoogie\Service\ServiceProvider;
use Symfony\Component\DependencyInjection\Container;
use function ICanBoogie\app;

class HooksTest extends \PHPUnit_Framework_TestCase
{
	public function test_service_provider_should_be_defined_during_app_boot()
	{
		$this->assertInstanceOf(ContainerProxy::class, ServiceProvider::defined());
	}

	public function test_compiled_container_should_be_deleted_on_app_clear_cache()
	{
		$app = app();
		$pathname = ContainerPathname::from($app);

		if (!file_exists($pathname))
		{
			$app->container;

			if (!file_exists($pathname))
			{
				$this->fail("Compiled container should have been created: $pathname");
			}
		}

		$app->clear_cache();

		if (file_exists($pathname))
		{
			$this->fail("Compiled container should have been deleted: $pathname");
		}

		// should be fine too
		$app->clear_cache();
	}

	public function test_app_container_getter()
	{
		$this->assertInstanceOf(Container::class, app()->container);
	}
}
