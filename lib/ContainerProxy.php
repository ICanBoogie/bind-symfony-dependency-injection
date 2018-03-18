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

use ICanBoogie\Accessor\AccessorTrait;
use ICanBoogie\Application;
use ICanBoogie\Autoconfig\Autoconfig;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Proxy to Symfony container.
 *
 * @property-read ContainerInterface $container
 */
class ContainerProxy
{
	use AccessorTrait;

	const SERVICE_APP = 'app';
	const SERVICE_CONTAINER = 'container';
	const CONFIG_FILENAME = 'services.yml';

	/**
	 * @var Application
	 */
	private $app;

	/**
	 * @var array
	 */
	private $config = [];

	/**
	 * @var ContainerInterface
	 */
	private $container;

	protected function get_container()
	{
		return $this->container ?: $this->container = $this->instantiate_container();
	}

	// @codeCoverageIgnoreStart
	public function __construct(Application $app, array $config)
	{
		$this->app = $app;
		$this->config = ContainerConfig::normalize($config);
	}
	// @codeCoverageIgnoreEnd

	public function __invoke(string $id): object
	{
		$container = $this->get_container();

		if ($id === self::SERVICE_CONTAINER) {
			return $container;
		}

		return $container->get($id);
	}

	private function instantiate_container(): ContainerInterface
	{
		$app = $this->app;
		$class = 'ApplicationContainer';
		$pathname = ContainerPathname::from($app);

		if (!$this->config[ContainerConfig::USE_CACHING] || !file_exists($pathname))
		{
			$container = $this->create_container_builder();
			$this->dump_container($container, $pathname, $class);
		}

		require $pathname;

		/* @var $container ContainerInterface */

		$container = new $class();
		$container->set(self::SERVICE_APP, $app);

		return $container;
	}

	private function create_container_builder(): ContainerBuilder
	{
		$container = new ContainerBuilder();

		$this->apply_extensions($container);
		$this->apply_services($container);

		$container->compile();
		$container->set(self::SERVICE_APP, $this->app);

		return $container;
	}

	private function apply_extensions(ContainerBuilder $container): void
	{
		$extensions = $this->collect_extensions();

		if (!$extensions)
		{
			return; // @codeCoverageIgnore
		}

		foreach ($extensions as $extension)
		{
			$container->registerExtension($extension);
			$container->loadFromExtension($extension->getAlias());
		}
	}

	private function collect_extensions(): array
	{
		$app = $this->app;
		$extensions = [];

		foreach ($this->config[ContainerConfig::EXTENSIONS] as $constructor)
		{
			$extensions[] = $constructor($app, $this);
		}

		return $extensions;
	}

	private function apply_services(ContainerBuilder $container): void
	{
		$collection = $this->collect_services();

		if (!$collection)
		{
			return; // @codeCoverageIgnore
		}

		$loader = new YamlFileLoader($container, new FileLocator(\getcwd()));

		foreach ($collection as $service_pathname)
		{
			$loader->load($service_pathname);
		}
	}

	private function collect_services(): array
	{
		$collection = [];

		foreach (\array_keys($this->app->config[Autoconfig::CONFIG_PATH]) as $path)
		{
			$pathname = $path . DIRECTORY_SEPARATOR . self::CONFIG_FILENAME;

			if (!\file_exists($pathname))
			{
				continue;
			}

			$collection[] = $pathname;
		}

		return $collection;
	}

	private function dump_container(ContainerBuilder $container, ContainerPathname $pathname, string $class): void
	{
		$dumper = new PhpDumper($container);

		\file_put_contents($pathname, $dumper->dump([ 'class' => $class ]));
	}
}
