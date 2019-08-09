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
use Psr\Container\ContainerInterface;
use function file_exists;
use function unlink;

final class Hooks
{
	/*
	 * Prototype methods
	 */

	// @codeCoverageIgnoreStart
	static public function on_app_boot(Application\BootEvent $event, Application $app): void
	{
		ServiceProvider::define(new ContainerProxy(
			$app,
			$app->configs[ContainerConfig::FRAGMENT_FOR_CONTAINER]
		));
	}
	// @codeCoverageIgnoreEnd

	static public function on_app_clear_cache(Application\ClearCacheEvent $event, Application $app): void
	{
		$pathname = ContainerPathname::from($app);

		if (!file_exists($pathname))
		{
			return;
		}

		unlink($pathname);
	}

	/*
	 * Prototype accessors
	 */

	static public function app_get_container(Application $app): ContainerInterface
	{
		return ServiceProvider::provide(ContainerProxy::ALIAS_CONTAINER);
	}
}
