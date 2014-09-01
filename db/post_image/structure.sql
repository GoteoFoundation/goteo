CREATE TABLE IF NOT EXISTS `post_image` (
  `post` bigint(20) NOT NULL,
  `image` int(10) unsigned NOT NULL,
  PRIMARY KEY (`post`,`image`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- campo imagen a nombre archivo
ALTER TABLE `post_image` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';