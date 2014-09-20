CREATE TABLE  `tag_lang` (
`id` bigint(20) unsigned NOT NULL,
`lang` varchar(2) NOT NULL,
`name` TINYTEXT NULL ,
UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;

-- pendiente de traducir
ALTER TABLE `tag_lang` ADD `pending` INT( 1 ) NULL DEFAULT '0' COMMENT 'Debe revisarse la traducción';
