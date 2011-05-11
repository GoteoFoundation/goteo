CREATE TABLE `promote` (
`node` VARCHAR( 50 ) NOT NULL ,
`project` VARCHAR( 50 ) NOT NULL ,
`title` TINYTEXT NULL ,
`description` TEXT NULL ,
`order` SMALLINT UNSIGNED NOT NULL DEFAULT '1',
 UNIQUE KEY `project_node` (`node`,`project`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Proyectos destacados';