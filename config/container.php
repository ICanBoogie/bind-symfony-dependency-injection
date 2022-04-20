<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ICanBoogie\Binding\SymfonyDependencyInjection\ConfigBuilder;
use ICanBoogie\Binding\SymfonyDependencyInjection\Extension\ApplicationExtension;

return function (ConfigBuilder $config): void {
    $config->add_extension(ApplicationExtension::class);
};
