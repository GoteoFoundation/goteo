CREATE TABLE IF NOT EXISTS `patron_order` (
`id` varchar(50) COLLATE utf8_general_ci NOT NULL,
`order` tinyint(3) unsigned NOT NULL DEFAULT '1',
PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Orden de los padrinos';

