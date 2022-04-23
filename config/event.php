<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ICanBoogie\Application;
use ICanBoogie\Application\BootEvent;
use ICanBoogie\Application\ClearCacheEvent;
use ICanBoogie\Binding\Event\ConfigBuilder;
use ICanBoogie\Binding\SymfonyDependencyInjection\EventCallbacks;

return fn(ConfigBuilder $config) => $config
    ->attach_to(Application::class, BootEvent::class, [ EventCallbacks::class, 'on_boot' ])
    ->attach_to(Application::class, ClearCacheEvent::class, [ EventCallbacks::class, 'on_clear_cache' ]);
