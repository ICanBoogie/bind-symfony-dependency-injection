<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie\Binding\SymfonyDependencyInjection\Extension;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use function ICanBoogie\app;

/**
 * @group integration
 */
final class ApplicationExtensionTest extends TestCase
{
	/**
	 * @var Container
	 */
	private $container;

	protected function setUp(): void
	{
		$container = &$this->container;

		if (!$container)
		{
			$container = new ContainerBuilder;
			$extension = new ApplicationExtension(app());
			$extension->load([], $container);
		}
	}

	/**
	 * @dataProvider provideService
	 *
	 * @param string $id
	 */
	public function testService($id)
	{
		$container = $this->container;
		$this->assertTrue($container->has($id));
	}

	public function provideService()
	{
		return $this->buildTestCases(<<<EOT
app
container
dispatcher
events
initial_request
logger
request
routes
session
EOT
		);
	}

	/**
	 * @dataProvider provideParameter
	 *
	 * @param string $param
	 */
	public function testParameter($param)
	{
		$this->assertTrue($this->container->hasParameter($param));
	}

	public function provideParameter()
	{
		return $this->buildTestCases(<<<EOT
app_path
app_paths
base_path
autoconfig_filters
cache.catalogs
cache.configs
cache.modules
config_constructor
config_path
error_handler
locale_path
repository
repository.cache
repository.cache.configs
repository.files
repository.tmp
repository.var
session
storage_for_configs
storage_for_vars
EOT
		);
	}

	private function buildTestCases($string)
	{
		return array_map(function ($param) {

			return [ $param ];

		}, explode("\n", $string));
	}
}
