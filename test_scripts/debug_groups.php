<?php
require __DIR__ . '/../vendor/autoload.php';
use Dibi\Connection;

$config = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'skp',
    'driver' => 'mysqli',
];

try {
    $db = new Connection($config);
    $groups = $db->query('SELECT * FROM admin_group')->fetchAll();
    echo "ID | Name | PID | Code Name\n";
    echo "---|------|-----|----------\n";
    foreach ($groups as $g) {
        echo "{$g->admin_group_id} | {$g->admin_group_name} | {$g->pid} | {$g->code_name}\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
