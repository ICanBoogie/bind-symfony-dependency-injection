# bind-symfony-dependency-injection

[![Release](https://img.shields.io/packagist/v/ICanBoogie/bind-symfony-dependency-injection.svg)](https://packagist.org/packages/ICanBoogie/bind-symfony-dependency-injection)
[![Code Quality](https://img.shields.io/scrutinizer/g/ICanBoogie/bind-symfony-dependency-injection.svg)](https://scrutinizer-ci.com/g/ICanBoogie/bind-symfony-dependency-injection)
[![Code Coverage](https://img.shields.io/coveralls/ICanBoogie/bind-symfony-dependency-injection.svg)](https://coveralls.io/r/ICanBoogie/bind-symfony-dependency-injection)
[![Packagist](https://img.shields.io/packagist/dt/ICanBoogie/bind-symfony-dependency-injection.svg)](https://packagist.org/packages/ICanBoogie/bind-symfony-dependency-injection)

Together with [icanboogie/service][], this package binds [symfony/dependency-injection][] to
[ICanBoogie][] and allows the container to be used to provide services.





## Obtaining services

Services can be obtained using a service reference or the container.

The following example demonstrates how services can be obtain using references:

```php
<?php

use function ICanBoogie\Service\ref;

$reference = ref('a_callable_service');
$result = $reference(1, 2, 3);

$reference = ref('a_service');
$service = $reference->resolve();
$service->do_something();
```

The following example demonstrates how a service can be obtained using the container itself:

```php
<?php

/* @var $app \ICanBoogie\Application */
/* @var $container \Symfony\Component\DependencyInjection\Container */

$container = $app->container;
$service = $container->get('a_service');
$service->do_something();
```





### Obtaining services bound to the application

Usually, ICanBoogie's components add getters to `ICanBoogie\Application` instances through the
[prototype system][], which means you can access the initial request using `$app->initial_request`
or the session using `$app->session`. Services defined this way are automatically accessible through
the container as well, which means they can be used as references `ref('session')` or obtained
through the container `$app->container->get('session')`.





### Obtaining config parameters

All application config parameters are available as container parameters e.g.
`$app->container->getParameter('app.repository.cache`)`.

**Note:** To avoid clashes, all application parameters are prefixed with `app.`.





## Defining services

Services are defined using `services.yml` files in `config` folders. They are collected when it's
time to create the container, just like regular [configuration files][].

The tests included in this package showcase how `services.yml` files can be defined in `all/config`
and `default/config`. Components and modules can use this feature to register their own services and
make them available to the application automatically.





## About the container proxy

The service provider defined during [Application::boot][] is an instance of [ContainerProxy][],
which only builds the service container when a service needs to be resolved.

The following example demonstrates how the service provider and the service container can be obtained:

```php
<?php

use ICanBoogie\Service\ServiceProvider;

/* @var $proxy \ICanBoogie\Binding\SymfonyDependencyInjection\ContainerProxy */

$proxy = ServiceProvider::defined();
$container = $proxy->container;
```





## Configuring the container

The container is configured using `container` configuration fragments:

```php
<?php

// config/container.php

namespace ICanBoogie\Binding\SymfonyDependencyInjection;

return [

	ContainerConfig::USE_CACHING => false,
	ContainerConfig::EXTENSIONS => [

		[ Extension\ApplicationExtension::class, 'from' ]

	]

];
```





### Defining container extensions

Container extensions are defined using [`EXTENSIONS`][]. Use an array of key/value pairs where _key_
is an optional identifier and _value_ a callable that constructs the extension.





----------



## Continuous Integration

The project is continuously tested by [GitHub actions](https://github.com/ICanBoogie/bind-symfony-dependency-injection/actions).

[![Tests](https://github.com/ICanBoogie/bind-symfony-dependency-injection/workflows/test/badge.svg?branch=master)](https://github.com/ICanBoogie/bind-symfony-dependency-injection/actions?query=workflow%3Atest)
[![Static Analysis](https://github.com/ICanBoogie/bind-symfony-dependency-injection/workflows/static-analysis/badge.svg?branch=master)](https://github.com/ICanBoogie/bind-symfony-dependency-injection/actions?query=workflow%3Astatic-analysis)
[![Code Style](https://github.com/ICanBoogie/bind-symfony-dependency-injection/workflows/code-style/badge.svg?branch=master)](https://github.com/ICanBoogie/bind-symfony-dependency-injection/actions?query=workflow%3Acode-style)



## Testing

Run `make test-container` to create and log into the test container, then run `make test` to run the
test suite. Alternatively, run `make test-coverage` to run the test suite with test coverage. Open
`build/coverage/index.html` to see the breakdown of the code coverage.



## License

**icanboogie/bind-symfony-dependency-injection** is released under the [BSD-3-Clause](LICENSE).




[ContainerProxy]:               https://icanboogie.org/api/bind-symfony-dependency-injection/master/
[`USE_CACHING`]:                https://icanboogie.org/api/bind-symfony-dependency-injection/master/class-ICanBoogie.Binding.SymfonyDependencyInjection.ContainerConfig.html#USE_CACHING
[`EXTENSIONS`]:                 https://icanboogie.org/api/bind-symfony-dependency-injection/master/class-ICanBoogie.Binding.SymfonyDependencyInjection.ContainerConfig.html#EXTENSIONS
[documentation]:                https://icanboogie.org/api/service/master/

[ICanBoogie]:                   https://icanboogie.org
[prototype system]:             https://icanboogie.org/docs/4.0/prototypes
[Application::boot]:            https://icanboogie.org/docs/4.0/life-and-death#the-application-has-booted
[configuration files]:          https://icanboogie.org/docs/4.0/configuration

[icanboogie/service]:           https://github.com/ICanBoogie/Service/
[available on GitHub]:          https://github.com/ICanBoogie/bind-symfony-dependency-injection
[symfony/dependency-injection]: https://symfony.com/doc/current/components/dependency_injection.html
