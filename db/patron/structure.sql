CREATE TABLE `patron` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`node` VARCHAR( 50 ) NOT NULL ,
`project` VARCHAR( 50 ) NOT NULL ,
`user` VARCHAR( 50 ) NOT NULL ,
`link` TINYTEXT NULL ,
`order` SMALLINT UNSIGNED NOT NULL DEFAULT '1',
 UNIQUE KEY `user_project_node` (`node`,`project`,`user`),
  UNIQUE KEY `id` (`id`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Proyectos apadrinados';
