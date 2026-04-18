<?php declare(strict_types=1);

/** @var \Nette\DI\Container $container */
$container = require __DIR__ . '/bootstrap.php';

use Dibi\Connection;

$db = $container->getByType(Connection::class);

try {
    $db->query('
        CREATE TABLE IF NOT EXISTS `spec_param_page` (
            `spec_param_id` int(11) NOT NULL AUTO_INCREMENT,
            `page_id` int(11) NOT NULL,
            `name` varchar(255) NOT NULL,
            `value` varchar(255) NOT NULL,
            PRIMARY KEY (`spec_param_id`),
            KEY `page_id` (`page_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ');
    echo "Table spec_param_page created successfully.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
