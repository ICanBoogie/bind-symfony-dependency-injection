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
use function strlen;
use function strpos;
use function strtr;
use function substr;
use function uniqid;

final class ApplicationExtension extends Extension
{
	public const APP_SERVICE = 'app';
	private const GETTER_PREFIX = 'get_';
	private const LAZY_GETTER_PREFIX = 'lazy_get_';

	/**
	 * Create a new instance.
	 */
	static public function from(Application $app): self
	{
		return new self($app);
	}

	/**
	 * @var Application
	 */
	private $app;

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
			self::APP_SERVICE,
			(new Definition(Application::class))
				->setSynthetic(true)
				->setPublic(true)
		);

		$container
			->setAlias(Application::class, self::APP_SERVICE);

		$this->add_parameters($container);
		$this->add_services($container);
	}

	private function add_parameters(ContainerBuilder $container): void
	{
		foreach ($this->app->config as $param => $value)
		{
			$param = $this->normalize_param($param);
			$container->setParameter($param, $value);
		}
	}

	private function add_services(ContainerBuilder $container): void
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
				->setFactory([ new Reference(Application::class), '__get' ])
				->setArguments([ $id ])
				->setPublic(true);

			$container->setDefinition($id, $definition);
		}
	}

	private function normalize_param(string $param): string
	{
		return strtr($param, [

			' ' => '.',
			'/' => '.',
			'-' => '_',

		]);
	}
}
