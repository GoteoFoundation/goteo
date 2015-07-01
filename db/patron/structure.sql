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

-- Alter
ALTER TABLE `patron` ADD `active` INT(1) NOT NULL DEFAULT '0' AFTER `order`;
ALTER TABLE `patron` ADD `title` TINYTEXT NULL AFTER `user`;
ALTER TABLE `patron` ADD `description` TEXT NULL AFTER `title`;

-- el id como primary key mejor
ALTER TABLE `patron`
	DROP KEY `id` ,
	ADD PRIMARY KEY(`id`) ;

-- constrains
ALTER TABLE `patron` ADD FOREIGN KEY (`node`) REFERENCES `node`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT;
