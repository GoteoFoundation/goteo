CREATE TABLE IF NOT EXISTS `call_lang` (
`id` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
`lang` varchar(2) NOT NULL,
`description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci ,
`whom` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci ,
`apply` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci ,
`legal` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci ,
`subtitle` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci ,
 UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

