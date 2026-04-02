<?php

namespace App;

use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;

		$router[] = new Route('admin/<presenter>/<action>[/<id>]', [
			'module' => 'Admin',
			'presenter' => 'Dashboard',
			'action' => 'default'
		]);

		$router[] = new Route('<presenter>/<action>[/<id>]', [			'module' => 'Front',
			'presenter' => 'Home',
			'action' => 'default'
		]);

		return $router;
	}
}
