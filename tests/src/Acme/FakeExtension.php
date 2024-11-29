<?php

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
