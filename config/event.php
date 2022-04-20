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

return [

    BootEvent::for(Application::class) => [ EventCallbacks::class, 'on_boot' ],
    ClearCacheEvent::for(Application::class) => [ EventCallbacks::class, 'on_clear_cache' ],

];
