# CHANGELOG

# v5.0 to v6.0

### New requirements

- Requires PHP v8.2+
- Requires olvlvl/symfony-dependency-injection-proxy v6.0+

## Breaking changes

- The container proxy has been replaced by a container factory. The container is now created right away and returned. Also, the package doesn't deal with `ICanBoogie\Service` anymore and let ICanBoogie deal with it instead. The prototype method `Application::get_container` and `ApplicationBindings` are removed. The event listener for `BootEvent` is removed.

- The package uses the new config builder feature introduced by [ICanBoogie/Config][] v6.0. The
  configuration is now an instance of `Config` and no longer an array.

    Before:
    ```php
    <?php

    namespace ICanBoogie;

    use ICanBoogie\Binding\SymfonyDependencyInjection\ContainerConfig;

    /* @var Application $app */

    $app->configs[ContainerConfig::FRAGMENT_FOR_CONTAINER][ContainerConfig::USE_CACHING];
    ```

    After:
    ```php
    <?php

    namespace ICanBoogie;

    use ICanBoogie\Binding\SymfonyDependencyInjection\Config;
    use ICanBoogie\Binding\SymfonyDependencyInjection\ConfigBuilder;

    /* @var Application $app */

    $app->config_for_class(Config::class)->use_caching;
    ```

## New features

- `AppConfig` parameters are added as `app.config.*` parameters; for example `app.config.var`.


[ICanBoogie/Config]: https://github.com/ICanBoogie/Config/
