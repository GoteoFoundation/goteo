CREATE TABLE IF NOT EXISTS `category_lang` (
`id` bigint(20) unsigned NOT NULL,
`lang` varchar(2) NOT NULL,
`name` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
 UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;
