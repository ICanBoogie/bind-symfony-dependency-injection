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

use function file_exists;
use function unlink;

final class EventCallbacks
{
    public static function on_boot(Application\BootEvent $event): void
    {
        ServiceProvider::define(
            ContainerProxy::from($event->app)
        );
    }

    public static function on_clear_cache(Application\ClearCacheEvent $event): void
    {
        $pathname = (string) ContainerPathname::from($event->app);

        if (!file_exists($pathname)) {
            return;
        }

        unlink($pathname);

        $event->cleared("container ($pathname)");
    }
}
