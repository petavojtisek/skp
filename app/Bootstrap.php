<?php

namespace App;

use Nette\Bootstrap\Configurator;
define('DS', DIRECTORY_SEPARATOR);
define('PROJECT_ROOT_DIR', dirname(__DIR__, 1));
define('APP_DIR', PROJECT_ROOT_DIR . DS. 'app');
define('LOG_DIR', PROJECT_ROOT_DIR . DS. 'log');
define('TEMP_DIR', PROJECT_ROOT_DIR . DS. 'temp');
define('CONFIG_DIR', PROJECT_ROOT_DIR .DS. 'config');

class Bootstrap
{
	public static function boot(): Configurator
	{
		$configurator = new Configurator;
		$appDir = dirname(__DIR__);







		//$configurator->setDebugMode('secret@23.75.345.200'); // enable for your remote IP
		$configurator->enableTracy($appDir . '/log');

		$configurator->setTempDirectory($appDir . '/temp');

		$configurator->createRobotLoader()
			->addDirectory(__DIR__)
			->register();

		$configurator->addConfig($appDir . '/config/config.neon');
		$configurator->addConfig($appDir . '/config/config.local.neon');

		return $configurator;
	}
}
