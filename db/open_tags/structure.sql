CREATE TABLE IF NOT EXISTS `open_tag` (
`id` SERIAL NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
`order` tinyint(3) unsigned NOT NULL DEFAULT '1'
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Agrupacion de los proyectos';

