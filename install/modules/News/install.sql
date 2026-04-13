-- install.sql
CREATE TABLE IF NOT EXISTS `news` (
  `element_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `short_text` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `content` longtext COLLATE utf8_czech_ci,
  `image` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`element_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
