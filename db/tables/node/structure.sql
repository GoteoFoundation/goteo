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

-- sello en proyectos
ALTER TABLE `node` ADD `label` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Sello en proyectos';

-- image in channels home module
ALTER TABLE `node` ADD `home_img` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Imagen para módulo canales en home';

-- background color del módulo de owner
ALTER TABLE `node` ADD `owner_background` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Color de background módulo owner';

-- asesor por defecto del canal
ALTER TABLE `node` ADD `default_consultant` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Asesor por defecto para el proyecto';

-- limite de sponsors
ALTER TABLE `node` ADD `sponsors_limit` INT( 2 ) DEFAULT NULL COMMENT 'Número de sponsors permitidos para el canal';

-- contrains
ALTER TABLE `node` CHANGE `default_consultant` `default_consultant` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci NULL COMMENT 'Asesor por defecto para el proyecto', ADD FOREIGN KEY (`default_consultant`) REFERENCES `user`(`id`) ON UPDATE CASCADE ON DELETE SET NULL;

-- owner font color
ALTER TABLE `node` ADD `owner_font_color` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Color de fuente módulo owner';

-- owner social color grey or white
ALTER TABLE `node` ADD `owner_social_color` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Color de iconos sociales módulo owner';

