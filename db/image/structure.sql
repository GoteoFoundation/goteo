DROP TABLE IF EXISTS `image`;
CREATE TABLE IF NOT EXISTS `image` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_general_ci DEFAULT NULL,
  `type` varchar(20) COLLATE utf8_general_ci DEFAULT NULL,
  `size` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=3 ;

-- quitamos el campo content, las imagenes van a archivo
ALTER TABLE `image` DROP `content`;
