<?php
require __DIR__ . '/bootstrap.php';
try {
    $db = $container->getByType(Dibi\Connection::class);
    $db->query('ALTER TABLE file_manager ADD COLUMN extension VARCHAR(10) AFTER path');
    echo "Column 'extension' added successfully.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
