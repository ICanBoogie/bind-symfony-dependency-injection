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

use ICanBoogie\AppConfig;
use ICanBoogie\Application;

/**
 * Representation of a container pathname
 */
final class ContainerPathname
{
	const FILENAME = 'container-compiled.php';

	/**
	 * @param Application $app
	 *
	 * @return ContainerPathname
	 */
	static public function from(Application $app)
	{
		return new self($app->config[AppConfig::REPOSITORY_CACHE] . DIRECTORY_SEPARATOR . self::FILENAME);
	}

	/**
	 * @var string
	 */
	private $pathname;

	/**
	 * @param string $pathname
	 */
	private function __construct($pathname)
	{
		$this->pathname = $pathname;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->pathname;
	}
}
