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


-- indices
ALTER TABLE `promote` ADD INDEX `activos` ( `active` );

-- Constrains
ALTER TABLE `promote`
    ADD KEY `project`(`project`) ;
ALTER TABLE `promote`
    ADD CONSTRAINT `promote_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ,
    ADD CONSTRAINT `promote_ibfk_2`
    FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;


