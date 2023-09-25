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
