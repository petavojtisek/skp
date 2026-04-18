<?php declare(strict_types=1);

/** @var \Nette\DI\Container $container */
$container = require __DIR__ . '/bootstrap.php';

use Dibi\Connection;

$db = $container->getByType(Connection::class);
$table = $argv[1] ?? 'install';
$action = $argv[2] ?? 'data';

try {
    if ($action === 'schema') {
        $fields = $db->query("DESCRIBE %n", $table)->fetchAll();
        echo "Schema for table $table:\n";
        foreach ($fields as $field) {
            print_r($field->toArray());
        }
    } else {
        $rows = $db->query("SELECT * FROM %n", $table)->fetchAll();
        echo "Rows in table $table:\n";
        foreach ($rows as $row) {
            print_r($row->toArray());
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
