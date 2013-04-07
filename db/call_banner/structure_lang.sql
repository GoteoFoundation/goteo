CREATE TABLE IF NOT EXISTS `call_banner_lang` (
`id` INT(20) NOT NULL,
`lang` varchar(2) NOT NULL,
`name` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
 UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;