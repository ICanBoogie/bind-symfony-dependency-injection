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

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

final class Config
{
    /**
     * @param class-string<CompilerPassInterface>[] $compiler_passes
     *     Compiler passes give you an opportunity to manipulate other service definitions that have been registered
     *     with the service container. You can read about how to create them in the components section "Compiling the
     *     Container".
     * @param class-string<ExtensionInterface>[] $extensions
     * @param bool $use_caching
     *     Whether the compiled container should be cached.
     *
     *     **Note:** Changes to services won't apply until the compiled container is re-created. You can use
     *     `icanboogie clear cache` or delete the file for the compiled container to be updated.
     */
    public function __construct(
        public readonly array $compiler_passes = [],
        public readonly array $extensions = [],
        public readonly bool $use_caching = false
    ) {
    }
}
