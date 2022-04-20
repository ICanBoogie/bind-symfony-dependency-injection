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
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

interface ExtensionWithFactory extends ExtensionInterface
{
    public static function from(Application $app): ExtensionInterface;
}
