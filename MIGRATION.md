# Migration

# v5.0 to v6.0

## Breaking changes

- The package uses the new config builder feature introduced by [ICanBoogie/Config][] v6.0. The
  configuration is now an instance of [Config][] and no longer an array.

    ```php
    <?php

    namespace ICanBoogie;

    use ICanBoogie\Binding\SymfonyDependencyInjection\ContainerConfig;

    /* @var Application $app */

    $app->configs[ContainerConfig::FRAGMENT_FOR_CONTAINER][ContainerConfig::USE_CACHING];
    ```

    ```php
    <?php

    namespace ICanBoogie;

    use ICanBoogie\Binding\SymfonyDependencyInjection\ConfigBuilder;

    /* @var Application $app */

    $app->configs[ConfigBuilder::FRAGMENT_NAME]->use_caching;
    ```



[ICanBoogie/Config]: https://github.com/ICanBoogie/Config/
[Config]: lib/Config.php
