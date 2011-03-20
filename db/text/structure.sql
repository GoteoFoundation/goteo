CREATE TABLE IF NOT EXISTS `text` (
`id` VARCHAR( 50 ) NOT NULL ,
`lang` VARCHAR( 2 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'es',
`text` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
PRIMARY KEY ( `id` , `lang` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Textos multi-idioma';