<?php

namespace ICanBoogie\Binding\SymfonyDependencyInjection;

use ICanBoogie\Application;
use RuntimeException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Throwable;

use function array_keys;
use function assert;
use function basename;
use function bin2hex;
use function chmod;
use function dirname;
use function file_exists;
use function file_put_contents;
use function getcwd;
use function is_string;
use function is_subclass_of;
use function random_bytes;
use function rename;
use function sha1;
use function symlink;
use function tempnam;
use function umask;
use function unlink;

use const DIRECTORY_SEPARATOR;

/**
 * Creates the dependency-injection container.
 *
 * In production, the container is compiled to a PHP file that is shared by all processes (PHP-FPM
 * workers, CLI, etc.), possibly while another process is compiling a new version. The following
 * invariants make that safe:
 *
 * - The compiled file is named after the SHA-1 of its source (`container-compiled.<hash>.php`) and
 *   is never modified once written. A process that resolved it can always load it.
 * - The file is written to a temporary file, then renamed, so nobody can read a partial file ({@see write_atomically()}).
 * - {@see ContainerPathname} is a symbolic link to the current hashed file, and is switched atomically
 *   ({@see switch_link()}), so it always points to a complete file.
 * - Outdated hashed files, and temporary files left by crashed writers, are removed by {@see ContainerPathname::prune()}
 *   only once they are old enough that no process can still be about to load them.
 *
 * @codeCoverageIgnore
 */
final class ContainerFactory
{
    public const string CONFIG_FILENAME = 'services.yml';

    public static function from(Application $app): ContainerInterface
    {
        return new self(
            $app,
            $app->config_for_class(Config::class)
        )->container;
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
        $class = 'ApplicationContainer'; // @phpstan-ignore varTag.nativeType

        // Load the hashed file the link resolves to, never the link itself, so that clearing the
        // cache between the check and the require cannot break us.
        $target = $this->config->use_caching ? $pathname->resolve() : null;

        if (!$target) {
            $builder = $this->create_container_builder();
            $builder->compile();
            $target = self::dump_container($builder, $pathname, $class);
        }

        require $target;

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

    /**
     * Dumps the container to a file named after the hash of its source, and points the container link to it.
     *
     * The file is only written if it doesn't exist, and the link is only switched if it doesn't already
     * point to it, so nothing moves when the container didn't change.
     *
     * @return string The pathname of the hashed file.
     */
    private static function dump_container(
        ContainerBuilder $container,
        ContainerPathname $pathname,
        string $class
    ): string {
        $pathname->assert_writeable();

        $source = new PhpDumper($container)->dump([ 'class' => $class ]);
        assert(is_string($source));
        $target = $pathname->hashed(sha1($source));

        if (!file_exists($target)) {
            self::write_atomically($target, $source);
        }

        if ($pathname->resolve() !== $target) {
            self::switch_link((string) $pathname, $target);
        }

        $pathname->prune($target);

        return $target;
    }

    /**
     * Writes `$source` to `$target` so that `$target` either does not exist or is complete.
     *
     * The temporary file is created in the same directory as the target, because `rename()` is only atomic within a
     * filesystem. `tempnam()` uses `<target>.tmp` as a prefix and appends a random suffix, creating the file
     * exclusively, so concurrent writers never share a temporary file. The `.tmp` marker is deliberate: it keeps the
     * name matched by {@see ContainerPathname::prune()}, which removes the files left by crashed writers once they are
     * older than its grace period. A fully random name would leak them.
     *
     * `tempnam()` creates files with mode 0600, hence the `chmod()` to honor the umask like a regular file would.
     *
     * @throws RuntimeException if the file cannot be written.
     */
    private static function write_atomically(string $target, string $source): void
    {
        // Write to a temporary file then rename it, which is atomic, to avoid exposing a partial file.
        $temp = tempnam(dirname($target), basename($target) . '.tmp');

        if ($temp === false) {
            throw new RuntimeException("Unable to create a temporary file in '" . dirname($target) . "'");
        }

        try {
            if (file_put_contents($temp, $source) === false) {
                throw new RuntimeException("Unable to write temporary file '$temp'");
            }

            chmod($temp, 0666 & ~umask());

            if (!rename($temp, $target)) {
                throw new RuntimeException("Unable to rename '$temp' to '$target'");
            }
        } catch (Throwable $e) {
            @unlink($temp);

            throw $e;
        }
    }

    /**
     * Atomically points `$link` to `$target`, by renaming a new link over the old one.
     */
    private static function switch_link(string $link, string $target): void
    {
        $temp = $link . '.' . bin2hex(random_bytes(4)) . '.tmp';

        if (!symlink(basename($target), $temp)) {
            throw new RuntimeException("Unable to create link '$temp'");
        }

        if (!rename($temp, $link)) {
            @unlink($temp);

            throw new RuntimeException("Unable to rename '$temp' to '$link'");
        }
    }
}
