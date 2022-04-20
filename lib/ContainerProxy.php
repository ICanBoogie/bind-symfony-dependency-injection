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

use ICanBoogie\Accessor\AccessorTrait;
use ICanBoogie\Application;
use ICanBoogie\Autoconfig\Autoconfig;
use ICanBoogie\Binding\SymfonyDependencyInjection\Extension\ApplicationExtension;
use Psr\Container\ContainerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

use function array_keys;
use function file_exists;
use function file_put_contents;
use function getcwd;
use function is_string;

/**
 * Proxy to Symfony container.
 *
 * @property-read ContainerInterface $container
 */
final class ContainerProxy implements ContainerInterface
{
    use AccessorTrait;

    public const ALIAS_APP = ApplicationExtension::APP_SERVICE;
    public const ALIAS_CONTAINER = 'container';
    private const CONFIG_FILENAME = 'services.yml';

    /**
     * @var array<string, mixed>
     */
    private array $config;

    private ContainerInterface $container;

    private function get_container(): ContainerInterface
    {
        return $this->container ??= $this->instantiate_container();
    }

    // @codeCoverageIgnoreStart

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly Application $app,
        array $config
    ) {
        $this->config = ContainerConfig::normalize($config);
    }

    // @codeCoverageIgnoreEnd

    /**
     * Note: We need the proxy to be a callable to satisfy `ICanBoogie\Service\ServiceProvider`.
     */
    public function __invoke(string $id): object
    {
        return $this->get($id);
    }

    public function get(string $id)
    {
        return match ($id) {
            self::ALIAS_APP, Application::class => $this->app,
            self::ALIAS_CONTAINER => $this->get_container(),
            default => $this->get_container()->get($id),
        };
    }

    public function has(string $id): bool
    {
        return match ($id) {
            self::ALIAS_APP, Application::class, self::ALIAS_CONTAINER => true,
            default => $this->get_container()->has($id),
        };
    }

    private function instantiate_container(): ContainerInterface
    {
        $app = $this->app;
        $class = 'ApplicationContainer';
        $pathname = ContainerPathname::from($app);

        if (!$this->config[ContainerConfig::USE_CACHING] || !file_exists($pathname)) {
            $builder = $this->create_container_builder();
            $builder->compile();
            $this->dump_container($builder, $pathname, $class);
        }

        require $pathname;

        /* @var $container \Symfony\Component\DependencyInjection\ContainerInterface */

        $container = new $class(); // @phpstan-ignore-line
        $container->set(Application::class, $app); // @phpstan-ignore-line

        return $container; // @phpstan-ignore-line
    }

    private function create_container_builder(): ContainerBuilder
    {
        $container = new ContainerBuilder();

        $this->apply_services($container);
        $this->apply_compiler_passes($container);
        $this->apply_extensions($container);

        return $container;
    }

    private function apply_compiler_passes(ContainerBuilder $builder): void
    {
        foreach ($this->compiler_passes() as $compiler_pass) {
            $compiler_pass->process($builder);
        }
    }

    private function apply_extensions(ContainerBuilder $container): void
    {
        foreach ($this->extensions() as $extension) {
            $container->registerExtension($extension);
            $container->loadFromExtension($extension->getAlias());
        }
    }

    /**
     * @return iterable<CompilerPassInterface>
     */
    private function compiler_passes(): iterable
    {
        /* @var class-string<CompilerPassInterface> $class */

        foreach ($this->config[ContainerConfig::COMPILER_PASSES] as $class) {
            yield new $class(); // @phpstan-ignore-line
        }
    }

    /**
     * @return iterable<Extension>
     */
    private function extensions(): iterable
    {
        $app = $this->app;

        foreach ($this->config[ContainerConfig::EXTENSIONS] as $constructor) {
            yield $constructor($app, $this);
        }
    }

    private function apply_services(ContainerBuilder $container): void
    {
        $collection = $this->collect_services();

        if (!$collection) {
            return; // @codeCoverageIgnore
        }

        $cwd = getcwd();
        assert(is_string($cwd));
        $loader = new YamlFileLoader($container, new FileLocator($cwd));

        foreach ($collection as $service_pathname) {
            $loader->load($service_pathname);
        }
    }

    /**
     * @return string[] Path names of the `services.yml` files collections.
     */
    private function collect_services(): array
    {
        $collection = [];

        foreach (array_keys($this->app->config[Autoconfig::CONFIG_PATH]) as $path) {
            $pathname = $path . DIRECTORY_SEPARATOR . self::CONFIG_FILENAME;

            if (!file_exists($pathname)) {
                continue;
            }

            $collection[] = $pathname;
        }

        return $collection;
    }

    private function dump_container(ContainerBuilder $container, ContainerPathname $pathname, string $class): void
    {
        $dumper = new PhpDumper($container);

        file_put_contents($pathname, $dumper->dump([ 'class' => $class ]));
    }
}
