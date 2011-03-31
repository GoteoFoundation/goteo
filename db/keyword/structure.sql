CREATE TABLE IF NOT EXISTS keyword (
`id` INT( 12 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`project` VARCHAR( 50 ) NOT NULL ,
`keyword` TINYTEXT NOT NULL ,
`category` BOOLEAN NOT NULL DEFAULT '0'
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Keywords de proyectos';

ALTER TABLE `keyword` DROP `category` ;