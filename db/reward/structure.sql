CREATE TABLE IF NOT EXISTS `reward` (
`id` INT( 12 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`project` VARCHAR( 50 ) NOT NULL ,
`reward` TINYTEXT NOT NULL ,
`type` VARCHAR( 50 ) NOT NULL ,
`icon` VARCHAR( 50 ) NULL ,
`license` VARCHAR( 50 ) NULL ,
`amount` INT( 5 ) NULL ,
`units` INT( 5 ) NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Retornos colectivos e individuales';

ALTER TABLE `reward` ADD `description` TINYTEXT NULL AFTER `reward` ;
ALTER TABLE `reward` CHANGE `reward` `reward` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;