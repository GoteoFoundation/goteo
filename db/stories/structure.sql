CREATE TABLE `stories` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`node` VARCHAR( 50 ) NOT NULL ,
`project` VARCHAR( 50 ) DEFAULT NULL ,
`order` SMALLINT UNSIGNED NOT NULL DEFAULT '1',
`image` INT( 10 ) NULL,
`active` INT( 1 ) NOT NULL DEFAULT '0',
`title` TINYTEXT,
`description` TEXT,
`review` TEXT,
`url` TINYTEXT,
`post` BIGINT( 20 ) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Historias existosas';
