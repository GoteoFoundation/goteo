CREATE TABLE IF NOT EXISTS `license` (
`id` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`description` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
`group` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'grupo de restriccion de menor a mayor',
`url` VARCHAR(256) DEFAULT NULL,
`order` TINYINT DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Licencias de distribucion';