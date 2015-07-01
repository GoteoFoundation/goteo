CREATE TABLE `banner` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`node` VARCHAR( 50 ) NOT NULL ,
`project` VARCHAR( 50 ) NOT NULL ,
`order` SMALLINT UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
 UNIQUE KEY `project_node` (`node`,`project`),
  UNIQUE KEY `id` (`id`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Proyectos en banner superior';

-- alters
ALTER TABLE `banner` ADD `image` INT( 10 ) NULL ;

-- banners sin proyecto
ALTER TABLE `banner` DROP INDEX `id` ;
ALTER TABLE `banner` DROP INDEX `project_node` ;
ALTER TABLE `banner` CHANGE `project` `project` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ;
ALTER TABLE `banner` ADD `active` INT(1) NOT NULL DEFAULT '0' ;
ALTER TABLE `banner` ADD `title` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL ;
ALTER TABLE `banner` ADD `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL ;
ALTER TABLE `banner` ADD `url` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL ;

-- campo imagen a nombre archivo
ALTER TABLE `banner` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

-- constrains
UPDATE banner SET project=NULL WHERE project='';
ALTER TABLE `banner` ADD FOREIGN KEY (`node`) REFERENCES `node`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
                     ADD FOREIGN KEY (`project`) REFERENCES `project`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT;
