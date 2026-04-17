-- install.sql
CREATE TABLE IF NOT EXISTS `system_constants` (
  `system_constant_id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `value` longtext,
  PRIMARY KEY (`system_constant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
