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
use olvlvl\SymfonyDependencyInjectionProxy\ProxyDumper;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

use function array_keys;
use function assert;
use function file_exists;
use function file_put_contents;
use function getcwd;
use function is_string;
use function is_subclass_of;

use const DIRECTORY_SEPARATOR;

/**
 * Creates the dependency-injection container.
 *
 * @codeCoverageIgnore
 */
final class ContainerFactory
{
    public const CONFIG_FILENAME = 'services.yml';

    public static function from(Application $app): ContainerInterface
    {
        return (new self(
            $app,
            $app->config_for_class(Config::class)
        ))->container;
    }

    private readonly ContainerInterface $container;

    private function __construct(
        private readonly Application $app,
        private readonly Config $config
    ) {
        $this->container = $this->instantiate_container();
    }

    private function instantiate_container(): ContainerInterface
    {
        $app = $this->app;
        $pathname = ContainerPathname::from($app);
        /** @var class-string<ContainerInterface> $class */
        $class = 'ApplicationContainer';

        if (!$this->config->use_caching || !file_exists($pathname)) {
            $builder = $this->create_container_builder();
            $builder->compile();
            self::dump_container($builder, $pathname, $class);
        }

        require $pathname;

        $container = new $class();
        $container->set(Application::class, $app);

        return $container;
    }

    private function create_container_builder(): ContainerBuilder
    {
        $container = new ContainerBuilder();

        $this->apply_parameters($container);
        $this->apply_services($container);
        $this->apply_compiler_passes($container);
        $this->apply_extensions($container);

        return $container;
    }

    private function apply_parameters(ContainerBuilder $container): void
    {
        $config = $this->app->config;

        $container->setParameter("app.config.var", $config->var);
        $container->setParameter("app.config.var_cache", $config->var_cache);
        $container->setParameter("app.config.var_cache_configs", $config->var_cache_configs);
        $container->setParameter("app.config.var_files", $config->var_files);
        $container->setParameter("app.config.var_lib", $config->var_lib);
        $container->setParameter("app.config.var_tmp", $config->var_tmp);
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
        foreach ($this->config->compiler_passes as $class) {
            yield new $class();
        }
    }

    /**
     * @return iterable<ExtensionInterface>
     */
    private function extensions(): iterable
    {
        $app = $this->app;

        foreach ($this->config->extensions as $constructor) {
            if (is_subclass_of($constructor, ExtensionWithFactory::class)) {
                yield $constructor::from($app);

                continue;
            }

            yield new $constructor();
        }
    }

    private function apply_services(ContainerBuilder $container): void
    {
        $collection = $this->collect_services();

        if (!$collection) {
            return;
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

        foreach (array_keys($this->app->autoconfig->config_paths) as $path) {
            $pathname = $path . DIRECTORY_SEPARATOR . self::CONFIG_FILENAME;

            if (!file_exists($pathname)) {
                continue;
            }

            $collection[] = $pathname;
        }

        return $collection;
    }

    private static function dump_container(
        ContainerBuilder $container,
        ContainerPathname $pathname,
        string $class
    ): void {
        $pathname->assert_writeable();

        $dumper = new PhpDumper($container);
        $dumper->setProxyDumper(new ProxyDumper());

        file_put_contents($pathname, $dumper->dump([ 'class' => $class ]));
    }
}
