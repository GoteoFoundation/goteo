CREATE TABLE `promote` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`node` VARCHAR( 50 ) NOT NULL ,
`project` VARCHAR( 50 ) NOT NULL ,
`title` TINYTEXT NULL ,
`description` TEXT NULL ,
`order` SMALLINT UNSIGNED NOT NULL DEFAULT '1',
 UNIQUE KEY `project_node` (`node`,`project`),
  UNIQUE KEY `id` (`id`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Proyectos destacados';

-- alter
ALTER TABLE `promote` ADD `id` SERIAL NOT NULL FIRST ;
ALTER TABLE `promote` ADD PRIMARY KEY ( `id` ) ;

ALTER TABLE `promote` ADD `active` INT(1) NOT NULL DEFAULT '0' ;
