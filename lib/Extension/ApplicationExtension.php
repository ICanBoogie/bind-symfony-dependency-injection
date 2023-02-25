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
use Symfony\Component\DependencyInjection\Extension\Extension;

use function assert;
use function is_string;
use function strtr;

final class ApplicationExtension extends Extension implements ExtensionWithFactory
{
    public static function from(Application $app): self
    {
        return new self($app);
    }

    private function __construct(
        private readonly Application $app
    ) {
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $this->add_parameters($container);
    }

    private function add_parameters(ContainerBuilder $container): void
    {
        foreach ($this->app->autoconfig as $param => $value) {
            assert(is_string($param));

            $param = $this->normalize_param($param);
            $container->setParameter("app.$param", $value);
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
