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

use PHPUnit\Framework\TestCase;

use function uniqid;

/**
 * @group unit
 */
final class ContainerConfigTest extends TestCase
{
    public function test_should_synthesize_container_config(): void
    {
        $config = ContainerConfig::synthesize([

            [

                ContainerConfig::USE_CACHING => false,
                ContainerConfig::EXTENSIONS => [

                    'one' => uniqid(),
                    'two' => $a2 = uniqid(),

                ],

                ContainerConfig::COMPILER_PASSES => [

                    $a3 = uniqid(),
                    $a4 = uniqid(),

                ]
            ],

            [

                ContainerConfig::USE_CACHING => true,
                ContainerConfig::EXTENSIONS => [

                    'one' => $b1 = uniqid(),
                    'three' => $b2 = uniqid(),

                ]

            ],

            [

                ContainerConfig::COMPILER_PASSES => [

                    $c1 = uniqid(),
                    $c2 = uniqid(),

                ]

            ]

        ]);

        $this->assertSame([

            ContainerConfig::USE_CACHING => true,
            ContainerConfig::EXTENSIONS => [

                'one' => $b1,
                'two' => $a2,
                'three' => $b2

            ],
            ContainerConfig::COMPILER_PASSES => [ $a3, $a4, $c1, $c2 ]

        ], $config);
    }

    public function test_should_normalize_config(): void
    {
        $this->assertSame([

            ContainerConfig::USE_CACHING => false,
            ContainerConfig::EXTENSIONS => [],
            ContainerConfig::COMPILER_PASSES => [],

        ], ContainerConfig::normalize([]));
    }
}
