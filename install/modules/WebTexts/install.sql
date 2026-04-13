-- install.sql
CREATE TABLE `web_text` (
 `web_text_id` int NOT NULL AUTO_INCREMENT,
 `code` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
 `text` longtext COLLATE utf8mb4_bin,
 PRIMARY KEY (`web_text_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


