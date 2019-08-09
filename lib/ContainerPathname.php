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
	private const FILENAME = 'container-compiled.php';

	static public function from(Application $app): self
	{
		return new self($app->config[AppConfig::REPOSITORY_CACHE] . DIRECTORY_SEPARATOR . self::FILENAME);
	}

	/**
	 * @var string
	 */
	private $pathname;

	private function __construct(string $pathname)
	{
		$this->pathname = $pathname;
	}

	public function __toString(): string
	{
		return $this->pathname;
	}
}
