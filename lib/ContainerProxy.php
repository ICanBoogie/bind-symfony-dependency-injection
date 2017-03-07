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
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Proxy to Symfony container.
 *
 * @property-read Container $container
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
	 * @var Container
	 */
	private $container;

	/**
	 * @return Container
	 */
	protected function get_container()
	{
		return $this->container ?: $this->container = $this->instantiate_container();
	}

	/**
	 * @codeCoverageIgnoreStart
	 *
	 * @param Application $app
	 * @param array $config
	 */
	public function __construct(Application $app, array $config)
	{
		$this->app = $app;
		$this->config = ContainerConfig::normalize($config);
	}
	// @codeCoverageIgnoreEnd

	/**
	 * @param string $id Service identifier
	 *
	 * @return object
	 */
	public function __invoke($id)
	{
		return $this->get_container()->get($id);
	}

	/**
	 * @return ContainerBuilder
	 */
	private function instantiate_container()
	{
		$app = $this->app;
		$class = 'ApplicationContainer';
		$pathname = ContainerPathname::from($app);

		if (!$this->config[ContainerConfig::USE_CACHING] || !file_exists($pathname))
		{
			$container = $this->create_container();
			$this->dump_container($container, $pathname, $class);
		}

		require $pathname;

		/* @var $container ContainerBuilder */

		$container = new $class();
		$container->set(self::SERVICE_APP, $app);
		$container->set(self::SERVICE_CONTAINER, $container);

		return $container;
	}

	/**
	 * @return ContainerBuilder
	 */
	private function create_container()
	{
		$container = new ContainerBuilder();

		$this->apply_extensions($container);
		$this->apply_services($container);

		$container->compile();
		$container->set(self::SERVICE_APP, $this->app);

		return $container;
	}

	/**
	 * @param ContainerBuilder $container
	 */
	private function apply_extensions(ContainerBuilder $container)
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

	/**
	 * @return array
	 */
	private function collect_extensions()
	{
		$app = $this->app;
		$extensions = [];

		foreach ($this->config[ContainerConfig::EXTENSIONS] as $constructor)
		{
			$extensions[] = $constructor($app, $this);
		}

		return $extensions;
	}

	/**
	 * @param ContainerBuilder $container
	 */
	private function apply_services(ContainerBuilder $container)
	{
		$collection = $this->collect_services();

		if (!$collection)
		{
			return; // @codeCoverageIgnore
		}

		$loader = new YamlFileLoader($container, new FileLocator(getcwd()));

		foreach ($collection as $service_pathname)
		{
			$loader->load($service_pathname);
		}
	}

	/**
	 * @return array
	 */
	private function collect_services()
	{
		$collection = [];

		foreach (array_keys($this->app->config[Autoconfig::CONFIG_PATH]) as $path)
		{
			$pathname = $path . DIRECTORY_SEPARATOR . self::CONFIG_FILENAME;

			if (!file_exists($pathname))
			{
				continue;
			}

			$collection[] = $pathname;
		}

		return $collection;
	}

	/**
	 * @param ContainerBuilder $container
	 * @param ContainerPathname $pathname
	 * @param string $class
	 */
	private function dump_container(ContainerBuilder $container, ContainerPathname $pathname, $class)
	{
		$dumper = new PhpDumper($container);

		file_put_contents($pathname, $dumper->dump([ 'class' => $class ]));
	}
}
