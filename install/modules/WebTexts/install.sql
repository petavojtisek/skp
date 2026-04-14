-- install.sql
CREATE TABLE IF NOT EXISTS `web_text` (
  `web_text_id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `text` longtext,
  PRIMARY KEY (`web_text_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
