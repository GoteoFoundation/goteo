CREATE TABLE IF NOT EXISTS `user_lang` (
`id` varchar(50) COLLATE utf8_general_ci NOT NULL,
`lang` varchar(2) NOT NULL,
`about` text COLLATE utf8_general_ci DEFAULT NULL,
`keywords` tinytext COLLATE utf8_general_ci DEFAULT NULL,
`contribution` text COLLATE utf8_general_ci DEFAULT NULL,
 UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;