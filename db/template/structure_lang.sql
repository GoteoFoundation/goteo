CREATE TABLE `template_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext NULL,
  `text` text NULL,
 UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;


-- pendiente de traducir
ALTER TABLE `template_lang` ADD `pending` INT( 1 ) NULL DEFAULT '0' COMMENT 'Debe revisarse la traducción';

-- CONSTRAINS
ALTER TABLE `template_lang` ADD FOREIGN KEY (`id`) REFERENCES `template`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE `template_lang` DROP INDEX `id_lang`;
ALTER TABLE `template_lang` ADD PRIMARY KEY (`id`, `lang`);
