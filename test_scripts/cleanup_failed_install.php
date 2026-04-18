<?php declare(strict_types=1);

/** @var \Nette\DI\Container $container */
$container = require __DIR__ . '/bootstrap.php';

use Dibi\Connection;

$db = $container->getByType(Connection::class);

echo "Cleaning up partial install...\n";

// 1. Delete module
$db->query("DELETE FROM `module` WHERE `module_name` = %s", 'ContentVersion');
echo "Module record deleted.\n";

// 2. Delete install
$db->query("DELETE FROM `install` WHERE `module_name` = %s", 'ContentVersion');
echo "Install record deleted.\n";

// 3. Drop tables
$db->query("DROP TABLE IF EXISTS `content_version` ");
$db->query("DROP TABLE IF EXISTS `version` ");
echo "Tables dropped.\n";

// 4. Remove rights just in case
$db->query("DELETE FROM `module_right` WHERE `module_id` NOT IN (SELECT `module_id` FROM `module`)");
$db->query("DELETE FROM `module_group_right` WHERE `module_id` NOT IN (SELECT `module_id` FROM `module`)");
echo "Dangling rights cleaned.\n";

echo "Cleanup complete.\n";
