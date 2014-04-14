CREATE TABLE IF NOT EXISTS `patron_order` (
`id` SERIAL NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`order` tinyint(3) unsigned NOT NULL DEFAULT '1'
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Orden de los padrinos';

