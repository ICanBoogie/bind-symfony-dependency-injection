# bind-symfony-dependency-injection

[![Release](https://img.shields.io/packagist/v/ICanBoogie/bind-symfony-dependency-injection.svg)](https://packagist.org/packages/ICanBoogie/bind-symfony-dependency-injection)
[![Build Status](https://img.shields.io/travis/ICanBoogie/bind-symfony-dependency-injection.svg)](http://travis-ci.org/ICanBoogie/bind-symfony-dependency-injection)
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
`$app->container->getParameter('repository.cache`)`.





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





## Requirements

The package requires PHP 5.6 or later.





## Installation

The recommended way to install this package is through [Composer](http://getcomposer.org/):

```
$ composer require icanboogie/bind-symfony-dependency-injection
```





### Cloning the repository

The package is [available on GitHub][], its repository can be cloned with the following command
line:

	$ git clone https://github.com/ICanBoogie/bind-symfony-dependency-injection.git





## Documentation

The package is documented as part of the [ICanBoogie][] framework [documentation][]. You can
generate the documentation for the package and its dependencies with the `make doc` command. The
documentation is generated in the `build/docs` directory. [ApiGen](http://apigen.org/) is required.
The directory can later be cleaned with the `make clean` command.





## Testing

The test suite is ran with the `make test` command. [PHPUnit](https://phpunit.de/) and
[Composer](http://getcomposer.org/) need to be globally available to run the suite. The command
installs dependencies as required. The `make test-coverage` command runs test suite and also creates
an HTML coverage report in `build/coverage`. The directory can later be cleaned with the `make
clean` command.

The package is continuously tested by [Travis CI](http://about.travis-ci.org/).

[![Build Status](https://img.shields.io/travis/ICanBoogie/bind-symfony-dependency-injection.svg)](http://travis-ci.org/ICanBoogie/bind-symfony-dependency-injection)
[![Code Coverage](https://img.shields.io/coveralls/ICanBoogie/bind-symfony-dependency-injection.svg)](https://coveralls.io/r/ICanBoogie/bind-symfony-dependency-injection)





## License

**icanboogie/bind-symfony-dependency-injection** is licensed under the New BSD License - See the
*[LICENSE](LICENSE) file for details.





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
