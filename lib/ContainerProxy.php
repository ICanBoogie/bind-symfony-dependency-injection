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
use ICanBoogie\Binding\SymfonyDependencyInjection\Extension\ApplicationExtension;
use Psr\Container\ContainerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use function array_keys;
use function file_exists;
use function file_put_contents;
use function getcwd;

/**
 * Proxy to Symfony container.
 *
 * @property-read ContainerInterface $container
 */
final class ContainerProxy
{
	use AccessorTrait;

	public const ALIAS_APP = ApplicationExtension::APP_SERVICE;
	public const ALIAS_CONTAINER = 'container';
	private const CONFIG_FILENAME = 'services.yml';

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
		switch ($id) {
			case self::ALIAS_APP: return $this->app;
			case self::ALIAS_CONTAINER: return $this->get_container();
			default: return $this->get_container()->get($id);
		}
	}

	private function instantiate_container(): ContainerInterface
	{
		$app = $this->app;
		$class = 'ApplicationContainer';
		$pathname = ContainerPathname::from($app);

		if (!$this->config[ContainerConfig::USE_CACHING] || !file_exists($pathname))
		{
			$container = $this->create_container_builder();
			$container->compile();
			$this->dump_container($container, $pathname, $class);
		}

		require $pathname;

		/* @var $container \Symfony\Component\DependencyInjection\ContainerInterface */

		$container = new $class();
		$container->set(self::ALIAS_APP, $app);

		return $container;
	}

	private function create_container_builder(): ContainerBuilder
	{
		$container = new ContainerBuilder();

		$this->apply_extensions($container);
		$this->apply_services($container);

		return $container;
	}

	private function apply_extensions(ContainerBuilder $container): void
	{
		foreach ($this->collect_extensions() as $extension)
		{
			$container->registerExtension($extension);
			$container->loadFromExtension($extension->getAlias());
		}
	}

	/**
	 * @return Extension[]
	 */
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

		$loader = new YamlFileLoader($container, new FileLocator(getcwd()));

		foreach ($collection as $service_pathname)
		{
			$loader->load($service_pathname);
		}
	}

	private function collect_services(): array
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

	private function dump_container(ContainerBuilder $container, ContainerPathname $pathname, string $class): void
	{
		$dumper = new PhpDumper($container);

		file_put_contents($pathname, $dumper->dump([ 'class' => $class ]));
	}
}
