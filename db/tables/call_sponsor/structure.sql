CREATE TABLE `call_sponsor` (
`id` SERIAL NOT NULL AUTO_INCREMENT ,
`call` varchar(50) NOT NULL,
`name` TINYTEXT NOT NULL ,
`url` TINYTEXT NULL ,
`image` INT( 10 ) NULL ,
`order` INT( 11 ) NOT NULL DEFAULT '1',
PRIMARY KEY ( `id` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Patrocinadores de convocatorias';

-- campo imagen a nombre archivo
ALTER TABLE `call_sponsor` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';


-- Main sponsor
ALTER TABLE `call_sponsor` ADD `main` BOOLEAN NOT NULL DEFAULT TRUE COMMENT 'Sponsor main';

ALTER TABLE `call_sponsor` ADD FOREIGN KEY (`call`) REFERENCES `call`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
