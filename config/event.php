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
use ICanBoogie\Application\BootEvent;
use ICanBoogie\Application\ClearCacheEvent;

use function ICanBoogie\Event\qualify_type;

/**
 * @uses Hooks::on_app_boot
 * @uses Hooks::on_app_clear_cache
 */

$hooks = Hooks::class . '::';

return [

    qualify_type(Application::class, BootEvent::TYPE) => $hooks . 'on_app_boot',
    qualify_type(Application::class, ClearCacheEvent::TYPE) => $hooks . 'on_app_clear_cache',

];
