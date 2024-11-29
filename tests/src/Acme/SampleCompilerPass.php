<?php

namespace Test\ICanBoogie\Binding\SymfonyDependencyInjection\Acme;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SampleCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $container->setParameter('compiler_pass_parameter', 'Hello world!');
    }
}
