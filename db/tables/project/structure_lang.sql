CREATE TABLE IF NOT EXISTS `project_lang` (
`id` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
`lang` varchar(2) NOT NULL,
`description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci ,
`motivation` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci ,
`about` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci ,
`goal` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci ,
`related` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci ,
`keywords` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci ,
`media` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci ,
`subtitle` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci ,
 UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

-- video motivacion
ALTER TABLE `project_lang` ADD `video` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `motivation` ;

-- nueva seccion contenido
ALTER TABLE `project_lang` ADD `reward` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `related` ;

-- pendiente de traducir
ALTER TABLE `project_lang` ADD `pending` INT( 1 ) NULL DEFAULT '0' COMMENT 'Debe revisarse la traducción';

ALTER TABLE `project_lang` ADD `social_commitment_description` TEXT COMMENT 'Social commitment of the project';

-- constrains
ALTER TABLE `project_lang` ADD FOREIGN KEY (`id`) REFERENCES `project`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
