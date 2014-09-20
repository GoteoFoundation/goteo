CREATE TABLE IF NOT EXISTS `info_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext NULL,
  `text` longtext NULL,
 UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- alters
ALTER TABLE `info_lang` ADD `legend` TEXT NULL ;

-- pendiente de traducir
ALTER TABLE `info_lang` ADD `pending` INT( 1 ) NULL DEFAULT '0' COMMENT 'Debe revisarse la traducción';
