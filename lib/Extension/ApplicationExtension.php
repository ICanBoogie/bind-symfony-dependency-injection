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

use ICanBoogie\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

class ApplicationExtension extends Extension
{
	const GETTER_PREFIX = 'get_';
	const LAZY_GETTER_PREFIX = 'lazy_get_';

	/**
	 * Create a new instance.
	 *
	 * @param Application $app
	 *
	 * @return static
	 */
	static public function from(Application $app)
	{
		return new static($app);
	}

	/**
	 * @var Application
	 */
	private $app;

	/**
	 * @param Application $app
	 */
	public function __construct(Application $app)
	{
		$this->app = $app;
	}

	/**
	 * @inheritdoc
	 */
	public function load(array $configs, ContainerBuilder $container)
	{
		$container->setDefinition(
			'app',
			(new Definition(Application::class))
				->setSynthetic(true)
		);

		$this->add_parameters($container);
		$this->add_services($container);
	}

	/**
	 * @param ContainerBuilder $container
	 */
	private function add_parameters(ContainerBuilder $container)
	{
		foreach ($this->app->config as $param => $value)
		{
			$param = $this->normalize_param($param);
			$container->setParameter($param, $value);
		}
	}

	/**
	 * @param ContainerBuilder $container
	 */
	private function add_services(ContainerBuilder $container)
	{
		foreach ($this->app->prototype as $method => $callable)
		{
			if (strpos($method, self::LAZY_GETTER_PREFIX) === 0)
			{
				$id = substr($method, strlen(self::LAZY_GETTER_PREFIX));
			}
			elseif (strpos($method, self::GETTER_PREFIX) === 0)
			{
				$id = substr($method, strlen(self::GETTER_PREFIX));
			}
			else
			{
				continue;
			}

			$definition = (new Definition('ICanBoogie\Dummy' . uniqid()))
				->setFactory([ new Reference('app'), '__get' ])
				->setArguments([ $id ]);

			$container->setDefinition($id, $definition);
		}
	}

	/**
	 * @param string $param
	 *
	 * @return string
	 */
	private function normalize_param($param)
	{
		return strtr($param, [

			' ' => '.',
			'/' => '.',
			'-' => '_',

		]);
	}
}
