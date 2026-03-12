<?php declare(strict_types=1);

/** @var \Nette\DI\Container $container */
$container = require __DIR__ . '/bootstrap.php';

use Dibi\Connection;

$db = $container->getByType(Connection::class);

if (isset($argv[1])) {
    $res = $db->query($argv[1]);
    if ($res instanceof \Dibi\Result) {
        $rows = $res->fetchAll();
        foreach ($rows as $row) {
            print_r((array)$row);
        }
    } else {
        echo "Affected rows: " . $db->getAffectedRows() . "\n";
    }
} else {
    echo "Usage: php check_lookup.php \"SQL QUERY\"\n";
}
