CREATE TABLE `template` (
`id` SERIAL NOT NULL PRIMARY KEY,
`name` TINYTEXT NOT NULL ,
`purpose` TINYTEXT NOT NULL ,
`title` TINYTEXT NOT NULL ,
`text` TEXT NOT NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Plantillas emails automáticos';


-- Agrupacion de usos de las plantillas
ALTER TABLE `template` ADD `group` VARCHAR( 50 ) NOT NULL DEFAULT 'general' COMMENT 'Agrupación de uso' AFTER `name`;
