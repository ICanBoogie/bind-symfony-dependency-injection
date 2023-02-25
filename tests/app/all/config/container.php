<?php

namespace Test\ICanBoogie\Binding\SymfonyDependencyInjection;

use ICanBoogie\Binding\SymfonyDependencyInjection\ConfigBuilder;
use Test\ICanBoogie\Binding\SymfonyDependencyInjection\Acme\SampleCompilerPass;

return fn(ConfigBuilder $config) => $config
    ->add_compiler_pass(SampleCompilerPass::class);
