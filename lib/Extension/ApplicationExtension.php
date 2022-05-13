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
use ICanBoogie\Binding\SymfonyDependencyInjection\ExtensionWithFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

use function assert;
use function is_string;
use function strlen;
use function strtr;
use function substr;
use function uniqid;

final class ApplicationExtension extends Extension implements ExtensionWithFactory
{
    public const APP_SERVICE = 'app';
    private const GETTER_PREFIX = 'get_';
    private const LAZY_GETTER_PREFIX = 'lazy_get_';

    public static function from(Application $app): self
    {
        return new self($app);
    }

    private function __construct(
        private readonly Application $app
    ) {
    }

    // @phpstan-ignore-next-line
    public function load(array $configs, ContainerBuilder $container): void
    {
        $container->setDefinition(
            Application::class,
            (new Definition(Application::class))
                ->setSynthetic(true)
        );

        $container
            ->setAlias(self::APP_SERVICE, Application::class)
            ->setPublic(true);

        $this->add_parameters($container);
        $this->add_services($container);
    }

    private function add_parameters(ContainerBuilder $container): void
    {
        foreach ($this->app->auto_config as $param => $value) {
            assert(is_string($param));

            $param = $this->normalize_param($param);
            $container->setParameter("app.$param", $value);
        }
    }

    private function add_services(ContainerBuilder $container): void
    {
        foreach ($this->app->prototype as $method => $callable) {
            if (str_starts_with($method, self::LAZY_GETTER_PREFIX)) {
                $id = substr($method, strlen(self::LAZY_GETTER_PREFIX));
            } elseif (str_starts_with($method, self::GETTER_PREFIX)) {
                $id = substr($method, strlen(self::GETTER_PREFIX));
            } else {
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
