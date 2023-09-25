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

use ICanBoogie\Binding\SymfonyDependencyInjection\ContainerPathname;
use PHPUnit\Framework\TestCase;

use function ICanBoogie\app;

/**
 * @group integration
 */
final class EventListener extends TestCase
{
    public function test_dumped_container_is_deleted_on_clear_cache(): void
    {
        $app = app();
        $pathname = (string) ContainerPathname::from($app);
        $this->assertFileExists($pathname);

        $app->clear_cache();
        $this->assertFileDoesNotExist($pathname);

        // should be fine too
        $app->clear_cache();
    }
}
