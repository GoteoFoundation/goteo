CREATE TABLE IF NOT EXISTS `cost` (
`id` INT( 12 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`project` VARCHAR( 50 ) NOT NULL ,
`cost` TINYTEXT NOT NULL ,
`type` VARCHAR( 50 ) NOT NULL DEFAULT 'task',
`amount` INT( 5 ) NOT NULL DEFAULT '0',
`required` BOOLEAN NOT NULL DEFAULT '0',
`from` DATE NULL ,
`until` DATE NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Desglose de costes de proyectos';