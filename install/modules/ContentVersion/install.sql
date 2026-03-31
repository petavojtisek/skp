-- install.sql
CREATE TABLE IF NOT EXISTS `content_version` (
  `element_id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text COLLATE utf8_czech_ci,
  `name` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `active` tinyint(1) DEFAULT '0',
  `created_dt` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`element_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

CREATE TABLE IF NOT EXISTS `version` (
  `component_id` int(11) NOT NULL,
  `element_id` int(11) NOT NULL,
  INDEX (`component_id`, `element_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
