CREATE TABLE `sponsor` (
`id` SERIAL NOT NULL AUTO_INCREMENT ,
`name` TINYTEXT NOT NULL ,
`url` TINYTEXT NULL ,
`image` INT( 10 ) NULL ,
`order` INT( 11 ) NOT NULL DEFAULT '1',
PRIMARY KEY ( `id` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Patrocinadores';

-- Alters
ALTER TABLE `sponsor` ADD `node` VARCHAR( 50 ) NOT NULL;
-- en consecuencia
UPDATE `sponsor` set node = 'goteo';

-- campo imagen a nombre archivo
ALTER TABLE `sponsor` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

-- Constrain
DELETE FROM `sponsor` WHERE `node` NOT IN (SELECT id FROM `nodes`);
ALTER TABLE `sponsor` DROP INDEX `id`, ADD FOREIGN KEY (`node`) REFERENCES `node`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
