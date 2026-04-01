-- install.sql
CREATE TABLE IF NOT EXISTS `content_version` (
  `element_id` int(11) NOT NULL,
  `content` text COLLATE utf8_czech_ci,
  PRIMARY KEY (`element_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

CREATE TABLE IF NOT EXISTS `version` (
  `component_id` int(11) NOT NULL,
  `element_id` int(11) NOT NULL,
  INDEX (`component_id`, `element_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
