<?php

namespace Test\ICanBoogie\Binding\SymfonyDependencyInjection\Acme;

use LogicException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class FakeCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        throw new LogicException();
    }
}
