<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie\Binding\SymfonyDependencyInjection\Extension;

use function ICanBoogie\app;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ApplicationExtensionTest extends \PHPUnit_Framework_TestCase
{
	const SERVICES = 'app container dispatcher events initial_request logger request routes session';

	public function testGetters()
	{
		$container = new ContainerBuilder;

		$extension = new ApplicationExtension(app());
		$extension->load([], $container);

		foreach (explode(' ', self::SERVICES) as $id)
		{
			$this->assertTrue($container->has($id));
		}
	}
}
