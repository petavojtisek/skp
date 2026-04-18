<?php declare(strict_types=1);

/** @var \Nette\DI\Container $container */
$container = require __DIR__ . '/bootstrap.php';

use Dibi\Connection;

$db = $container->getByType(Connection::class);

try {
    $db->query('ALTER TABLE `page_in_group_user` ADD `page_in_group_user_id` INT NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`page_in_group_user_id`)');
    echo "Table page_in_group_user updated with primary key.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
