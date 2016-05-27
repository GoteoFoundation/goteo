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

-- post no obligatorio
ALTER TABLE `stories` CHANGE `post` `post` BIGINT( 20 ) UNSIGNED NULL DEFAULT NULL;

-- campo imagen a nombre archivo
ALTER TABLE `stories` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

-- constrains
ALTER TABLE `stories` ADD FOREIGN KEY (`node`) REFERENCES `node`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT;

-- campo para imagen en landing de monedero

ALTER TABLE `stories` ADD `pool_image` varchar(255) DEFAULT NULL;

-- campo para marcar si aparece en landing de monedero o no

ALTER TABLE `stories` ADD `pool` INT( 1 ) NOT NULL DEFAULT '0';

-- campo posicion texto si es banner general

ALTER TABLE `stories` ADD `text_position` varchar(50) DEFAULT NULL;