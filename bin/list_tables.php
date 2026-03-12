<?php declare(strict_types=1);

/** @var \Nette\DI\Container $container */
$container = require __DIR__ . '/bootstrap.php';

use Dibi\Connection;

$db = $container->getByType(Connection::class);
$tables = $db->query('SHOW TABLES')->fetchPairs(null, 'Tables_in_skp');

echo "Tables in skp database:\n";
foreach ($tables as $table) {
    echo $table . "\n";
}
