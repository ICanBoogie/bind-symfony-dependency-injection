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

class ContainerConfig
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

	/**
	 * Define container extensions using an array of key/value pairs, where _key_ is an identifier
	 * and _value_ a callable with the following signature:
	 *
	 *     \Symfony\Component\DependencyInjection\Extension\ExtensionInterface (\ICanBoogie\Application $app)
	 */
	const EXTENSIONS = 'extensions';

	/**
	 * @param array $fragments
	 *
	 * @return array
	 */
	static public function synthesize(array $fragments)
	{
		$use_caching = false;
		$extensions = [];

		foreach ($fragments as $fragment)
		{
			if (isset($fragment[self::USE_CACHING]))
			{
				$use_caching = $fragment[self::USE_CACHING];
			}

			if (isset($fragment[self::EXTENSIONS]))
			{
				$extensions[] = $fragment[self::EXTENSIONS];
			}
		}

		if ($extensions)
		{
			$extensions = array_merge(...$extensions);
		}

		return [

			self::USE_CACHING => $use_caching,
			self::EXTENSIONS => $extensions

		];
	}

	/**
	 * @param array $config
	 *
	 * @return array
	 */
	static public function normalize(array $config)
	{
		return $config + [

			self::USE_CACHING => false,
			self::EXTENSIONS => [],

		];
	}
}
