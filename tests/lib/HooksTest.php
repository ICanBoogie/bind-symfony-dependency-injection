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
use function ICanBoogie\app;
use function var_dump;

/**
 * @group integration
 */
class HooksTest extends TestCase
{
	public function test_service_provider_should_be_defined_during_app_boot()
	{
		$this->assertInstanceOf(ContainerProxy::class, ServiceProvider::defined());
	}

	public function test_compiled_container_should_be_deleted_on_app_clear_cache()
	{
		$app = app();
		$pathname = (string) ContainerPathname::from($app);

		if (!file_exists($pathname))
		{
			$app->container;

			$this->assertFileExists($pathname);
		}

		$app->clear_cache();
		$this->assertFileNotExists($pathname);

		// should be fine too
		$app->clear_cache();
	}

	public function test_app_container_getter()
	{
		$this->assertInstanceOf(Container::class, app()->container);
	}

	public function test_get_app()
	{
		$this->assertSame(app(), app()->container->get('app'));
	}
}
