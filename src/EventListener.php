<?php

namespace ICanBoogie\Binding\SymfonyDependencyInjection;

use ICanBoogie\Application\ClearCacheEvent;

use function unlink;

final class EventListener
{
    /**
     * Unlinks the container link on {@link ClearCacheEvent}. The hashed files are not removed right away,
     * because running processes might be about to load them, they are pruned once old enough.
     */
    public static function on_clear_cache(ClearCacheEvent $event): void
    {
        $container = ContainerPathname::from($event->app);
        $pathname = (string) $container;

        if (!$container->exists()) {
            $container->prune();

            return;
        }

        unlink($pathname);
        $container->prune();

        $event->cleared("container ($pathname)");
    }
}
