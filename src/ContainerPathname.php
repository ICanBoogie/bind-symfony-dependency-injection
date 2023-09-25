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

use ICanBoogie\Application;
use RuntimeException;
use Stringable;

use function dirname;
use function file_exists;
use function is_writeable;

/**
 * Represents of a container pathname.
 */
final class ContainerPathname implements Stringable
{
    private const FILENAME = 'container-compiled.php';

    public static function from(Application $app): self
    {
        return new self($app->config->var_cache . self::FILENAME);
    }

    private function __construct(
        private readonly string $pathname
    ) {
    }

    public function __toString(): string
    {
        return $this->pathname;
    }

    public function assert_writeable(): void
    {
        $dir = dirname($this->pathname);

        if (!file_exists($dir)) {
            throw new RuntimeException("The directory '$dir' does not exist");
        }

        if (!is_writeable($dir)) {
            throw new RuntimeException("The directory '$dir' is not writeable");
        }
    }
}
