<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie;

chdir(__DIR__);

require __DIR__ . '/../vendor/autoload.php';

if (file_exists(__DIR__ . '/var/cache/container-compiled.php')) {
    unlink(__DIR__ . '/var/cache/container-compiled.php');
}

boot();

namespace Test\ICanBoogie\Binding\SymfonyDependencyInjection;

use DirectoryIterator;
use RegexIterator;

use function unlink;

const SANDBOX = __DIR__ . "/sandbox";

$di = new DirectoryIterator(SANDBOX);
$di = new RegexIterator($di, '/\.php$/');

foreach ($di as $file) {
    /** @var DirectoryIterator $file */

    unlink($file->getPathname());
}
