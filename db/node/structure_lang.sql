CREATE TABLE IF NOT EXISTS `node_lang` (
`id` varchar(50) COLLATE utf8_general_ci NOT NULL,
`lang` varchar(2) NOT NULL,
`subtitle` text COLLATE utf8_general_ci DEFAULT NULL,
`description` text COLLATE utf8_general_ci DEFAULT NULL,
 UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;