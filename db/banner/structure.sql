CREATE TABLE `banner` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`node` VARCHAR( 50 ) NOT NULL ,
`project` VARCHAR( 50 ) NOT NULL ,
`order` SMALLINT UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
 UNIQUE KEY `project_node` (`node`,`project`),
  UNIQUE KEY `id` (`id`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Proyectos en banner superior';
