CREATE TABLE IF NOT EXISTS `post` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `blog` bigint(20) unsigned NOT NULL,
  `title` tinytext,
  `text` longtext COMMENT 'texto de la entrada',
  `media` tinytext,
  `image` int(10) DEFAULT NULL,
  `date` date NOT NULL COMMENT 'fehca de publicacion',
  `order` int(11) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Entradas para la portada';

-- los alters
ALTER TABLE  `post` ADD  `blog` BIGINT UNSIGNED NOT NULL AFTER  `id`;
ALTER TABLE  `post` CHANGE  `description`  `text` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT  'texto de la entrada';
ALTER TABLE  `post` ADD  `image` INT( 10 ) NULL ,
ADD  `date` DATE NOT NULL;
ALTER TABLE `post` ADD `order` INT NULL DEFAULT '1';
ALTER TABLE `post` ADD `allow` BOOLEAN NOT NULL DEFAULT '1' COMMENT 'Permite comentarios';