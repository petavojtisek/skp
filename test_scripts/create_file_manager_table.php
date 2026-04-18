<?php
require __DIR__ . '/bootstrap.php';

try {
    $db = $container->getByType(Dibi\Connection::class);
    
    $db->query("CREATE TABLE IF NOT EXISTS `file_manager` (
      `file_id` int(11) NOT NULL AUTO_INCREMENT,
      `element_id` int(11) DEFAULT NULL,
      `source_type` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
      `file_type` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
      `original_name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
      `file_name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
      `path` varchar(255) COLLATE utf8_czech_ci NOT NULL,
      `mime_type` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
      `size` bigint(20) DEFAULT NULL,
      `created_dt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      `admin_id` int(11) DEFAULT NULL,
      PRIMARY KEY (`file_id`),
      KEY `element_lookup` (`source_type`, `element_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;");

    echo "Table 'file_manager' created successfully.\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
