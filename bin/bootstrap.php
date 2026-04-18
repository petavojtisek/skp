<?php declare(strict_types=1);

/**
 * Common bootstrap for CLI scripts in the skp project.
 * This file handles project initialization and returns the Nette DI container.
 */

require __DIR__ . '/../vendor/autoload.php';

// CLI optimizations
set_time_limit(0);
ini_set('memory_limit', '-1');



/** @var \Nette\DI\Container $container */
$container = \App\Bootstrap::boot()
	->createContainer();

return $container;
