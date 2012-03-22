CREATE TABLE `mail` (
`id` SERIAL NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`email` TINYTEXT NOT NULL ,
`html` LONGTEXT NOT NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Contenido enviado por email para el -si no ves-';


-- alters
ALTER TABLE `mail` ADD `template` int( 20 ) NULL ,
ADD `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ;

ALTER TABLE `mail` ADD `node` VARCHAR( 50 ) NULL AFTER `template` ;