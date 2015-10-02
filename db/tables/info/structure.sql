CREATE TABLE IF NOT EXISTS `info` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `node` VARCHAR(50) NOT NULL ,
  `title` tinytext,
  `text` longtext COMMENT 'texto de la entrada',
  `media` tinytext,
  `publish` BOOLEAN NOT NULL DEFAULT '0',
  `order` int(11) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Entradas about';

-- los alters
ALTER TABLE `info` ADD `legend` TEXT NULL ;

-- Campo calculado para imágenes de la galería
ALTER TABLE `info` ADD `gallery` VARCHAR( 2000 ) NULL COMMENT 'Galería de imagenes';

-- imagen principal
ALTER TABLE `info` ADD `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Imagen principal';

-- constrains
ALTER TABLE `info` ADD FOREIGN KEY (`node`) REFERENCES `node`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT;

--spread
ALTER TABLE `info` ADD `share_facebook` TINYTEXT, ADD `share_twitter` TINYTEXT;
ALTER TABLE `info_lang` ADD `share_facebook` TINYTEXT, ADD `share_twitter` TINYTEXT;
