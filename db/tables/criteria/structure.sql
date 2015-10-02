CREATE TABLE `criteria` (
`id` SERIAL NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`section` VARCHAR( 50 ) NOT NULL DEFAULT 'node',
`title` TINYTEXT NULL ,
`description` TEXT NULL ,
`order` TINYINT NOT NULL DEFAULT '1'
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Criterios de puntuación';