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

/**
 * @group unit
 */
final class ContainerConfigTest extends TestCase
{
    public function test_should_synthesize_container_config()
    {
        $config = ContainerConfig::synthesize([

            [

                ContainerConfig::USE_CACHING => false,
                ContainerConfig::EXTENSIONS => [

                    'one' => $f1 = uniqid(),
                    'two' => $f2 = uniqid(),

                ]

            ],

            [

                ContainerConfig::USE_CACHING => true,
                ContainerConfig::EXTENSIONS => [

                    'one' => $f3 = uniqid(),
                    'three' => $f4 = uniqid(),

                ]

            ]

        ]);

        $this->assertSame([

            ContainerConfig::USE_CACHING => true,
            ContainerConfig::EXTENSIONS => [

                'one' => $f3,
                'two' => $f2,
                'three' => $f4

            ]

        ], $config);
    }

    public function test_should_normalize_config()
    {
        $this->assertSame([

            ContainerConfig::USE_CACHING => false,
            ContainerConfig::EXTENSIONS => []

        ], ContainerConfig::normalize([]));
    }
}
