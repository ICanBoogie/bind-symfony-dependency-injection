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
use ICanBoogie\Service\ServiceProvider;
use Psr\Container\ContainerInterface;

final class PrototypeCallbacks
{
    public static function app_get_container(Application $app): ContainerInterface
    {
        return ServiceProvider::provide(ContainerProxy::ALIAS_CONTAINER); // @phpstan-ignore-line
    }
}
