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

use function array_merge;

final class ContainerConfig
{
    /**
     * Fragment name for the container configuration.
     */
    public const FRAGMENT_FOR_CONTAINER = 'container';

    /**
     * Whether the compiled container should be cached.
     *
     * **Note:** Changes to services won't apply until the compiled container is re-created.
     * You can use `icanboogie clear cache` or delete the file for the compiled container to
     * be updated.
     */
    public const USE_CACHING = 'use_caching';

    /**
     * Define container extensions using an array of key/value pairs, where _key_ is an identifier
     * and _value_ a callable with the following signature:
     *
     *     \Symfony\Component\DependencyInjection\Extension\ExtensionInterface (\ICanBoogie\Application $app)
     */
    public const EXTENSIONS = 'extensions';

    /**
     * Compiler passes give you an opportunity to manipulate other service definitions that have been registered with
     * the service container. You can read about how to create them in the components section "Compiling the Container".
     */
    public const COMPILER_PASSES = 'compiler_passes';

    /**
     * @param array<int, array<string, mixed>> $fragments
     *
     * @return array<string, mixed>
     */
    public static function synthesize(array $fragments): array
    {
        $use_caching = false;
        $extensions = [];
        $compiler_passes = [];

        foreach ($fragments as $fragment) {
            if (isset($fragment[self::USE_CACHING])) {
                $use_caching = $fragment[self::USE_CACHING];
            }

            $extensions[] = $fragment[self::EXTENSIONS] ?? [];
            $compiler_passes[] = $fragment[self::COMPILER_PASSES] ?? [];
        }

        if ($extensions) {
            $extensions = array_merge(...$extensions);
        }

        if ($compiler_passes) {
            $compiler_passes = array_merge(...$compiler_passes);
        }

        return [

            self::USE_CACHING => $use_caching,
            self::EXTENSIONS => $extensions,
            self::COMPILER_PASSES => $compiler_passes,

        ];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return array<string, mixed>
     */
    public static function normalize(array $config): array
    {
        return $config + [

            self::USE_CACHING => false,
            self::EXTENSIONS => [],
            self::COMPILER_PASSES => [],

        ];
    }
}
