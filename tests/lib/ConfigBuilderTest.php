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
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

use function uniqid;

/**
 * @group unit
 */
final class ConfigBuilderTest extends TestCase
{
    public function test_fail_on_invalid_extensions(): void
    {
        $builder = new ConfigBuilder();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches("/Extension must implement .+ExtensionInterface/");
        // @phpstan-ignore-next-line
        $builder->add_extension(uniqid());
    }

    public function test_fail_on_invalid_compiler_pass(): void
    {
        $builder = new ConfigBuilder();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches("/Compiler pass must implement .+CompilerPassInterface/");
        // @phpstan-ignore-next-line
        $builder->add_compiler_pass(uniqid());
    }

    public function test_build(): void
    {
        $builder = new ConfigBuilder();

        $builder->disable_caching();
        $builder->add_extension($a1 = $this->mockExtension()::class);
        $builder->add_extension($a2 = $this->mockExtension()::class);
        $builder->add_compiler_pass($a3 = $this->mockCompilerPass()::class);
        $builder->add_compiler_pass($a4 = $this->mockCompilerPass()::class);
        $builder->enable_caching();
        $builder->add_extension($b1 = $this->mockExtension()::class);
        $builder->add_extension($b2 = $this->mockExtension()::class);
        $builder->add_compiler_pass($c1 = $this->mockCompilerPass()::class);
        $builder->add_compiler_pass($c2 = $this->mockCompilerPass()::class);

        $this->assertEquals(
            new Config(
                [ $a3, $a4, $c1, $c2 ],
                [ $a1, $a2, $b1, $b2 ],
                true
            ),
            $builder->build()
        );
    }

    private function mockExtension(): ExtensionInterface
    {
        return $this->createMock(ExtensionInterface::class);
    }

    private function mockCompilerPass(): CompilerPassInterface
    {
        return $this->createMock(CompilerPassInterface::class);
    }
}
