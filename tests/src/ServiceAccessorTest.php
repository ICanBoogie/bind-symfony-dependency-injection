<?php

namespace Test\ICanBoogie\Binding\SymfonyDependencyInjection;

use ICanBoogie\ConfigProvider;
use ICanBoogie\Storage\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function ICanBoogie\app;

final class ServiceAccessorTest extends TestCase
{
    /**
     * @param class-string $class
     */
    #[DataProvider('provide_service')]
    public function test_service(string $id, string $class): void
    {
        $service = app()->service_for_id($id, $class);

        $this->assertInstanceOf($class, $service);
    }

    /**
     * @return array<array{ string, class-string }>
     */
    public static function provide_service(): array
    {
        return [

            [ 'test.app.vars', Storage::class ],
            [ 'test.config_provider', ConfigProvider::class ],

        ];
    }
}
