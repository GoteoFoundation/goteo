CREATE TABLE IF NOT EXISTS `cost_lang` (
`id` INT(20) NOT NULL,
`lang` varchar(2) NOT NULL,
`cost` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
`description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci ,
 UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

-- pendiente de traducir
ALTER TABLE `cost_lang` ADD `pending` INT( 1 ) NULL DEFAULT '0' COMMENT 'Debe revisarse la traducción';
