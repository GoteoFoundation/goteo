CREATE TABLE IF NOT EXISTS `interest` (
`id` SERIAL NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Intereses de los usuarios';