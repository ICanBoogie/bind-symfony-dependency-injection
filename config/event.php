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

/**
 * @uses Hooks::on_app_boot
 * @uses Hooks::on_app_clear_cache
 */

$hooks = Hooks::class . '::';

return [

	'ICanBoogie\Application::boot'        => $hooks . 'on_app_boot',
	'ICanBoogie\Application::clear_cache' => $hooks . 'on_app_clear_cache',

];
