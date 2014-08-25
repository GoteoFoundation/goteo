DROP TABLE IF EXISTS `user_image`;
CREATE TABLE IF NOT EXISTS `user_image` (
  `user` varchar(50) COLLATE utf8_general_ci NOT NULL,
  `image` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user`,`image`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- campo imagen a nombre archivo
ALTER TABLE `user_image` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';
