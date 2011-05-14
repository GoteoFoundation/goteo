CREATE TABLE IF NOT EXISTS `icon` (
`id` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`description` VARCHAR(50) NOT NULL DEFAULT 'description-icon-default' COMMENT 'id del texto que lo describe',
`group` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'exclusivo para grupo',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Iconos para retorno/recompensa';