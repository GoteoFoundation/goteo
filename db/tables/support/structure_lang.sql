CREATE TABLE IF NOT EXISTS `support_lang` (
`id` INT(20) NOT NULL,
`lang` varchar(2) NOT NULL,
`support` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
`description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci ,
 UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

-- pendiente de traducir
ALTER TABLE `support_lang` ADD `pending` INT( 1 ) NULL DEFAULT '0' COMMENT 'Debe revisarse la traducción';

-- indice proyecto
ALTER TABLE `support_lang` ADD `project` VARCHAR( 50 ) NOT NULL AFTER `id` ,
ADD INDEX ( `project` );

-- rellenar este campo
UPDATE support_lang SET support_lang.project = (SELECT support.project FROM support WHERE support.id = support_lang.id);

-- create foreign indexs
DELETE FROM support_lang WHERE id NOT IN (SELECT id FROM support);
ALTER TABLE `support_lang` CHANGE `id` `id` BIGINT(20) UNSIGNED NOT NULL, ADD FOREIGN KEY (`id`) REFERENCES `support`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
