<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\ICanBoogie\Binding\SymfonyDependencyInjection;

use ICanBoogie\Binding\SymfonyDependencyInjection\Config;
use PHPUnit\Framework\TestCase;
use Test\ICanBoogie\Binding\SymfonyDependencyInjection\Acme\FakeCompilerPass;
use Test\ICanBoogie\Binding\SymfonyDependencyInjection\Acme\FakeExtension;

final class ConfigTest extends TestCase
{
    public function test_export(): void
    {
        $config = new Config(
            compiler_passes: [
                FakeCompilerPass::class,
                FakeCompilerPass::class,
            ],
            extensions: [
                FakeExtension::class,
                FakeExtension::class,
            ],
            use_caching: true
        );

        $actual = SetStateHelper::export_import($config);

        $this->assertEquals($config, $actual);
    }
}
