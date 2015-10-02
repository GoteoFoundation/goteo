CREATE TABLE IF NOT EXISTS `glossary` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` tinytext,
  `text` longtext COMMENT 'texto de la entrada',
  `media` tinytext,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Entradas para el glosario';

-- los alters
ALTER TABLE `glossary` ADD `legend` TEXT NULL ;

-- Campo calculado para imágenes de la galería
ALTER TABLE `glossary` ADD `gallery` VARCHAR( 2000 ) NULL COMMENT 'Galería de imagenes';

-- imagen principal
ALTER TABLE `glossary` ADD `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Imagen principal';

-- depreacted gallery field
ALTER TABLE `glossary` DROP COLUMN `gallery`;
