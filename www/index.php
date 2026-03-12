<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

App\Bootstrap::boot()
	->createContainer()
	->getService('application')
	->run();
