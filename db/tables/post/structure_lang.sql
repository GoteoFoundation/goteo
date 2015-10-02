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

-- pendiente de traducir
ALTER TABLE `post_lang` ADD `pending` INT( 1 ) NULL DEFAULT '0' COMMENT 'Debe revisarse la traducción';

-- indice blog
ALTER TABLE `post_lang` ADD `blog` INT( 20 ) NOT NULL AFTER `id` ,
ADD INDEX ( `blog` );

-- rellenar este campo
UPDATE post_lang SET post_lang.blog = (SELECT post.blog FROM post WHERE post.id = post_lang.id);
