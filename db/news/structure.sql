CREATE TABLE `news` (
`id` SERIAL NOT NULL AUTO_INCREMENT ,
`title` TINYTEXT NOT NULL ,
`url` TINYTEXT NOT NULL ,
`order` INT( 11 ) NOT NULL DEFAULT '1',
PRIMARY KEY ( `id` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Noticias en la cabecera';


-- alters
ALTER TABLE `news` ADD `description` TEXT NULL COMMENT 'Entradilla' AFTER `title` ;

-- alter para banner prensa

ALTER TABLE `news` ADD `image` INT( 10 ) NULL ,
ADD `press_banner` BOOLEAN NULL DEFAULT '0' COMMENT 'Para aparecer en banner prensa',
ADD `media_name` TINYTEXT  NULL COMMENT 'Medio de prensa en que se publica';

-- campo imagen a nombre archivo
ALTER TABLE `news` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';
