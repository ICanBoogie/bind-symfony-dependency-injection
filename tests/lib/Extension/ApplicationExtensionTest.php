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
use function array_map;
use function explode;
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
app.app_path
app.app_paths
app.base_path
app.autoconfig_filters
app.cache.catalogs
app.cache.configs
app.cache.modules
app.config_constructor
app.config_path
app.error_handler
app.locale_path
app.repository
app.repository.cache
app.repository.cache.configs
app.repository.files
app.repository.tmp
app.repository.var
app.session
app.storage_for_configs
app.storage_for_vars
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
