CREATE TABLE  `tag_lang` (
`id` bigint(20) unsigned NOT NULL,
`lang` varchar(2) NOT NULL,
`name` TINYTEXT NULL ,
UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;
