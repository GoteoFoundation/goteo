CREATE TABLE IF NOT EXISTS `category` (
`id` SERIAL NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
`order` tinyint(3) unsigned NOT NULL DEFAULT '1'
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Categorias de los proyectos';


-- alters
ALTER TABLE `category` ADD `order` TINYINT UNSIGNED NOT NULL DEFAULT '1';
ALTER TABLE `category` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `category` ADD `social_commitment` VARCHAR(50) DEFAULT NULL COMMENT 'Social commitment';
