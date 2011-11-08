CREATE TABLE IF NOT EXISTS `post_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext NULL,
  `text` longtext NULL,
 UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- alters
ALTER TABLE `post_lang` ADD `legend` TEXT NULL ;
ALTER TABLE `post_lang` ADD `media` TINYTEXT NULL ;