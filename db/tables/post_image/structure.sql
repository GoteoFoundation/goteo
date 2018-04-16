CREATE TABLE IF NOT EXISTS `post_image` (
  `post` bigint(20) NOT NULL,
  `image` int(10) unsigned NOT NULL,
  PRIMARY KEY (`post`,`image`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- campo imagen a nombre archivo
ALTER TABLE `post_image` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

ALTER TABLE `post_image` CHANGE `post` `post` BIGINT(20) UNSIGNED NOT NULL;
DELETE FROM `post_image` WHERE `post` NOT IN (SELECT `id` FROM `post`);
ALTER TABLE `post_image` ADD FOREIGN KEY (`post`) REFERENCES `post`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

-- order
ALTER TABLE `post_image` ADD COLUMN `order` TINYINT(4) DEFAULT 1 NOT NULL AFTER `image`;
