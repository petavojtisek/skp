<?php declare(strict_types=1);

/** @var \Nette\DI\Container $container */
$container = require __DIR__ . '/bootstrap.php';

use Dibi\Connection;

$db = $container->getByType(Connection::class);

try {
    $fields = $db->query('SHOW COLUMNS FROM lookup_lang')->fetchPairs('Field', 'Field');
    echo "Columns in lookup_lang: " . implode(', ', $fields) . "\n";

    $rows = $db->select('*')->from('lookup_lang')->limit(5)->fetchAll();
    foreach ($rows as $row) {
        print_r((array)$row);
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
