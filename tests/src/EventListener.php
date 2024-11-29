<?php

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
