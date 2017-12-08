CREATE TABLE IF NOT EXISTS `info_image` (
  `info` bigint(20) NOT NULL,
  `image` int(10) unsigned NOT NULL,
  PRIMARY KEY (`info`,`image`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- campo imagen a nombre archivo
ALTER TABLE `info_image` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

-- order
ALTER TABLE `info_image` ADD COLUMN `order` TINYINT NULL AFTER `image`;
