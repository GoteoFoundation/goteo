CREATE TABLE IF NOT EXISTS `support` (
`id` INT( 12 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`project` VARCHAR( 50 ) NOT NULL ,
`support` TINYTEXT NOT NULL ,
`description` TEXT NOT NULL ,
`type` VARCHAR( 50 ) NOT NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Colaboraciones';
