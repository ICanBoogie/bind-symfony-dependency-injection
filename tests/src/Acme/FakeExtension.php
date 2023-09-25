<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\ICanBoogie\Binding\SymfonyDependencyInjection\Acme;

use LogicException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

final class FakeExtension implements ExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        throw new LogicException();
    }

    public function getNamespace()
    {
        throw new LogicException();
    }

    public function getXsdValidationBasePath()
    {
        throw new LogicException();
    }

    public function getAlias()
    {
        throw new LogicException();
    }
}
