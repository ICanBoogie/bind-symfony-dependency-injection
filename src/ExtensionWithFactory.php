<?php

namespace ICanBoogie\Binding\SymfonyDependencyInjection;

use ICanBoogie\Application;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * An interface for extensions that require access to the {@link Application} instance.
 */
interface ExtensionWithFactory extends ExtensionInterface
{
    public static function from(Application $app): ExtensionInterface;
}
