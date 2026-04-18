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
    $tables = $db->query('SHOW TABLES')->fetchPairs();

    $md = "# Database Schema\n\n";

    foreach ($tables as $table) {
        $md .= "## Table: `{$table}`\n\n";
        $md .= "| Field | Type | Null | Key | Default | Extra |\n";
        $md .= "|-------|------|------|-----|---------|-------|\n";

        $fields = $db->query("DESCRIBE `{$table}`")->fetchAll();
        foreach ($fields as $field) {
            $md .= "| {$field->Field} | {$field->Type} | {$field->Null} | {$field->Key} | " . var_export($field->Default, true) . " | {$field->Extra} |\n";
        }
        $md .= "\n";
    }

    $outputPath = __DIR__ . '/../.ai/database.md';
    file_put_contents($outputPath, $md);
    echo "Database schema updated in .ai/database.md\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
