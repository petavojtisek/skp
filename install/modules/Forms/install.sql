-- install.sql
CREATE TABLE IF NOT EXISTS `forms` (
  `element_id` int(11) NOT NULL,
  `form_component` varchar(255) NOT NULL,
  PRIMARY KEY (`element_id`),
  CONSTRAINT `fk_forms_element` FOREIGN KEY (`element_id`) REFERENCES `element` (`element_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
