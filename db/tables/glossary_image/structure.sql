CREATE TABLE IF NOT EXISTS `glossary_image` (
  `glossary` bigint(20) NOT NULL,
  `image` int(10) unsigned NOT NULL,
  PRIMARY KEY (`glossary`,`image`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- campo imagen a nombre archivo
ALTER TABLE `glossary_image` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

ALTER TABLE `glossary_image` CHANGE `glossary` `glossary` BIGINT(20) UNSIGNED NOT NULL;
ALTER TABLE `glossary_image` ADD FOREIGN KEY (`glossary`) REFERENCES `glossary`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

-- glossary
ALTER TABLE `glossary_image` ADD COLUMN `order` TINYINT NULL AFTER `image`;
