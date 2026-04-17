<?php

namespace App;

use Nette\Bootstrap\Configurator;

define('DS', DIRECTORY_SEPARATOR);
define('PROJECT_ROOT_DIR', dirname(__DIR__, 1));
define('APP_DIR', PROJECT_ROOT_DIR . DS . 'app');
define('LOG_DIR', PROJECT_ROOT_DIR . DS . 'log');
define('TEMP_DIR', PROJECT_ROOT_DIR . DS . 'temp');
define('CONFIG_DIR', PROJECT_ROOT_DIR . DS . 'config');

// List of IPs allowed to bypass maintenance mode
define('MAINTENANCE_WHITELIST', [
	'127.0.0.1',
	'::1',
	// 'YOUR_IP_HERE',
]);

class Bootstrap
{
	public static function boot(): Configurator
	{
		$appDir = dirname(__DIR__);

		// --- Maintenance Toggle ---
		$showMaintenance = false; // Set to true to activate maintenance mode
		// --------------------------

		if ($showMaintenance && !in_array($_SERVER['REMOTE_ADDR'] ?? null, MAINTENANCE_WHITELIST, true)) {
			if (file_exists($appDir . '/www/maintenance.html')) {
				require $appDir . '/www/maintenance.html';
			} else {
				header('HTTP/1.1 503 Service Unavailable');
				echo '<h1>503 Service Unavailable</h1><p>The site is under maintenance. Please come back later.</p>';
			}
			exit;
		}

		$configurator = new Configurator;

		// --- Debug Mode Toggle ---
		// Set to true to test production error pages (404, 500)
		$forceProductionMode = true;

		if ($forceProductionMode) {
			$configurator->setDebugMode(false);
		}
		// --------------------------

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
