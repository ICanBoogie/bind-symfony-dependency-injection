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

class Hooks
{
	/**
	 * @codeCoverageIgnoreStart
	 *
	 * @param Application\BootEvent $event
	 * @param Application $app
	 */
	static public function on_app_boot(Application\BootEvent $event, Application $app)
	{
		ServiceProvider::define(new ContainerProxy($app));
	}
	// @codeCoverageIgnoreEnd

	/**
	 * @param Application\ClearCacheEvent $event
	 * @param Application $app
	 */
	static public function on_app_clear_cache(Application\ClearCacheEvent $event, Application $app)
	{
		$pathname = ContainerPathname::from($app);

		if (!file_exists($pathname))
		{
			return;
		}

		unlink($pathname);
	}

	/**
	 * @param Application $app
	 *
	 * @return callable|null
	 */
	static public function app_get_container(Application $app)
	{
		return ServiceProvider::provide(ContainerProxy::SERVICE_CONTAINER);
	}
}
