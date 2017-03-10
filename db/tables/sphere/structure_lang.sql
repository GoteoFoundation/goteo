CREATE TABLE IF NOT EXISTS `sphere_lang` (
`id` bigint(20) unsigned NOT NULL,
`lang` varchar(2) NOT NULL,
`name` TEXT,
`pending` INT( 1 ) NULL DEFAULT '0',
 UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;