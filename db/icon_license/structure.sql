CREATE TABLE IF NOT EXISTS `icon_license` (
`icon` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`license` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
  UNIQUE KEY `icon` (`icon`,`license`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Licencias para cada icono, solo social';