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

interface ContainerConfig
{
	/**
	 * Fragment name for the container configuration.
	 */
	const FRAGMENT_FOR_CONTAINER = 'container';

	/**
	 * Whether the compiled container should be cached.
	 *
	 * **Note:** Changes to services won't apply until the compiled container is re-created.
	 * You can use `icanboogie clear cache` or delete the file for the compiled container to
	 * be updated.
	 */
	const USE_CACHING = 'use_caching';
}
