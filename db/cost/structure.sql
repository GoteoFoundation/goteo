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

ALTER TABLE `cost` ADD `description` TINYTEXT NULL AFTER `cost`;
ALTER TABLE `cost` CHANGE `cost` `cost` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;
ALTER TABLE `cost` CHANGE `required` `required` TINYINT( 1 ) NULL DEFAULT '0';
ALTER TABLE `cost` CHANGE `amount` `amount` INT( 5 ) NULL DEFAULT '0';