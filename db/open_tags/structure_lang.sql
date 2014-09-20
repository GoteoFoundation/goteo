CREATE TABLE IF NOT EXISTS `open_tag_lang` (
`id` bigint(20) unsigned NOT NULL,
`lang` varchar(2) NOT NULL,
`name` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
 UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

-- pendiente de traducir
ALTER TABLE `open_tag_lang` ADD `pending` INT( 1 ) NULL DEFAULT '0' COMMENT 'Debe revisarse la traducción';
