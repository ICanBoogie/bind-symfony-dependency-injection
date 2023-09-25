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

use ICanBoogie\Application\ClearCacheEvent;

use function file_exists;
use function unlink;

final class EventListener
{
    /**
     * Unlinks the dumped container on {@link ClearCacheEvent}.
     */
    public static function on_clear_cache(ClearCacheEvent $event): void
    {
        $pathname = (string) ContainerPathname::from($event->app);

        if (!file_exists($pathname)) {
            return;
        }

        unlink($pathname);

        $event->cleared("container ($pathname)");
    }
}
