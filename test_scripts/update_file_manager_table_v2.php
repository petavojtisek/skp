<?php
require __DIR__ . '/bootstrap.php';
/** @var Dibi\Connection $db */
$db = $container->getByType(Dibi\Connection::class);

try {
    $db->query("ALTER TABLE file_manager ADD COLUMN sort_order INT DEFAULT 0 AFTER size");
    $db->query("ALTER TABLE file_manager ADD COLUMN is_main TINYINT DEFAULT 0 AFTER sort_order");
    echo "Columns sort_order and is_main added to file_manager table.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
