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

use ICanBoogie\AppConfig;
use ICanBoogie\Application;
use Stringable;

/**
 * Represents of a container pathname.
 */
final class ContainerPathname implements Stringable
{
    private const FILENAME = 'container-compiled.php';

    public static function from(Application $app): self
    {
        return new self($app->config->repository_cache . self::FILENAME);
    }

    private function __construct(
        private readonly string $pathname
    ) {
    }

    public function __toString(): string
    {
        return $this->pathname;
    }
}
