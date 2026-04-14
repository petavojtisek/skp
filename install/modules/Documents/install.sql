-- install.sql
CREATE TABLE IF NOT EXISTS `documents` (
  `element_id` int(11) NOT NULL,
  `text` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `file_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`element_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
