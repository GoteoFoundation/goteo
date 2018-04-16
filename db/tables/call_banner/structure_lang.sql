CREATE TABLE IF NOT EXISTS `call_banner_lang` (
`id` INT(20) NOT NULL,
`lang` varchar(2) NOT NULL,
`name` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
 UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

-- pendiente de traducir
ALTER TABLE `call_banner_lang` ADD `pending` INT( 1 ) NULL DEFAULT '0' COMMENT 'Debe revisarse la traducción';

ALTER TABLE `call_banner_lang` CHANGE `id` `id` INT(11) UNSIGNED NOT NULL, ADD PRIMARY KEY (`id`, `lang`);
DELETE FROM call_banner_lang WHERE id NOT IN (SELECT id FROM call_banner);
ALTER TABLE `call_banner_lang` ADD FOREIGN KEY (`id`) REFERENCES `call_banner`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
