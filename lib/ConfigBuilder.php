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

use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

use function is_subclass_of;

final class ConfigBuilder implements \ICanBoogie\ConfigBuilder
{
    /**
     * Fragment name for the configuration.
     */
    public const FRAGMENT_NAME = 'container';

    public function build(): Config
    {
        return new Config(
            $this->compiler_passes,
            $this->extensions,
            $this->use_caching,
        );
    }

    /**
     * @var class-string<CompilerPassInterface>[] $compiler_passes
     */
    private array $compiler_passes = [];

    /**
     * @param class-string<CompilerPassInterface> $compiler_pass_class
     */
    public function add_compiler_pass(string $compiler_pass_class): void
    {
        if (!is_subclass_of($compiler_pass_class, CompilerPassInterface::class, true)) {
            throw new InvalidArgumentException("Compiler pass must implement " . CompilerPassInterface::class);
        }

        $this->compiler_passes[] = $compiler_pass_class;
    }

    /**
     * @var class-string<ExtensionInterface>[]
     */
    private array $extensions = [];

    /**
     * @param class-string<ExtensionInterface> $extension_class
     */
    public function add_extension(string $extension_class): void
    {
        if (!is_subclass_of($extension_class, ExtensionInterface::class, true)) {
            throw new InvalidArgumentException("Extension must implement " . ExtensionInterface::class);
        }

        $this->extensions[] = $extension_class;
    }

    private bool $use_caching = false;

    public function enable_caching(): void
    {
        $this->use_caching = true;
    }

    public function disable_caching(): void
    {
        $this->use_caching = false;
    }
}
