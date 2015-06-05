CREATE TABLE IF NOT EXISTS node (
  id varchar(50) NOT NULL,
  `name` varchar(256) NOT NULL,
  active tinyint(1) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Nodos';

-- Alters
ALTER TABLE `node` ADD `url` VARCHAR( 255 ) NOT NULL;
ALTER TABLE `node` 
	ADD `subtitle` TEXT NULL ,
	ADD `logo` INT( 20 ) UNSIGNED NULL ,
	ADD `location` VARCHAR( 100 ) NULL ,
	ADD `description` TEXT NULL ;

-- mail de contacto para impulsores
ALTER TABLE `node` ADD `email` VARCHAR( 255 ) NOT NULL AFTER `name`;

-- campo imagen a nombre archivo
ALTER TABLE `node` CHANGE `logo` `logo` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

-- mail de contacto para impulsores
ALTER TABLE `node` ADD `email` VARCHAR( 255 ) NOT NULL AFTER `name`;

-- perfiles sociales del nooo/canal
ALTER TABLE `node`
	  ADD `twitter`  tinytext COLLATE utf8_general_ci,
  	ADD `facebook` tinytext COLLATE utf8_general_ci,
    ADD `google` tinytext COLLATE utf8_general_ci,
  	ADD `linkedin` tinytext COLLATE utf8_general_ci;
