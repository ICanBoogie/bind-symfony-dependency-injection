<?php

namespace ICanBoogie\Binding\SymfonyDependencyInjection;

use ICanBoogie\Application;
use ICanBoogie\ConfigProvider;

final class Factory
{
    public static function build_config_provider(Application $app): ConfigProvider
    {
        return $app->configs;
    }
}
