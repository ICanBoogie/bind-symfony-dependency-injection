<?php

namespace ICanBoogie\Binding\SymfonyDependencyInjection;

use ICanBoogie\Application;

/**
 * A helper class to define services that are provided as properties on the application.
 */
final class ServiceAccessor
{
    public function __construct(
        private readonly Application $app
    ) {
    }

    public function get(string $id): object
    {
        return $this->app->$id;
    }
}
