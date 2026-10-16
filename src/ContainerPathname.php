<?php

namespace ICanBoogie\Binding\SymfonyDependencyInjection;

use ICanBoogie\Application;
use RuntimeException;
use Stringable;

use function basename;
use function dirname;
use function file_exists;
use function filemtime;
use function glob;
use function is_file;
use function is_link;
use function is_writeable;
use function readlink;
use function time;
use function unlink;

/**
 * Represents the container pathname.
 *
 * The pathname is a symbolic link to a file named after the hash of its content
 * (`container-compiled.<hash>.php`). The link is switched atomically when the container changes, and
 * a file is never modified once written, so a process that resolved the link can always load the file.
 */
final class ContainerPathname implements Stringable
{
    private const FILENAME = 'container-compiled.php';
    private const HASHED_PATTERN = 'container-compiled.*.php*';

    /**
     * Hashed files older than this many seconds, and not targeted by the link, are removed.
     */
    private const PRUNE_AFTER = 600;

    public static function from(Application $app): self
    {
        return new self($app->config->var_cache . self::FILENAME);
    }

    private function __construct(
        private readonly string $pathname
    ) {
    }

    public function __toString(): string
    {
        return $this->pathname;
    }

    /**
     * @param string $hash Hash of the container source.
     */
    public function hashed(string $hash): string
    {
        return dirname($this->pathname) . '/container-compiled.' . $hash . '.php';
    }

    /**
     * Returns the pathname of the file targeted by the link, if it exists.
     */
    public function resolve(): ?string
    {
        $target = @readlink($this->pathname);

        if ($target === false) {
            return null;
        }

        $target = dirname($this->pathname) . '/' . basename($target);

        return is_file($target) ? $target : null;
    }

    /**
     * Whether the link exists, even if dangling.
     */
    public function exists(): bool
    {
        return is_link($this->pathname) || file_exists($this->pathname);
    }

    /**
     * Removes the hashed files, and the temporary files left by crashed writers, that are not the current target and are old enough that no
     * process can still be about to load them.
     */
    public function prune(?string $keep = null): void
    {
        $keep ??= $this->resolve();

        foreach (glob(dirname($this->pathname) . '/' . self::HASHED_PATTERN) ?: [] as $file) {
            if ($file === $keep) {
                continue;
            }

            $mtime = @filemtime($file);

            if ($mtime !== false && time() - $mtime > self::PRUNE_AFTER) {
                @unlink($file);
            }
        }
    }

    public function assert_writeable(): void
    {
        $dir = dirname($this->pathname);

        if (!file_exists($dir)) {
            throw new RuntimeException("The directory '$dir' does not exist");
        }

        if (!is_writeable($dir)) {
            throw new RuntimeException("The directory '$dir' is not writeable");
        }
    }
}
