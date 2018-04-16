CREATE TABLE IF NOT EXISTS `call_lang` (
`id` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
`lang` varchar(2) NOT NULL,
`description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci ,
`whom` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci ,
`apply` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci ,
`legal` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci ,
`subtitle` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci ,
 UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Dosier informativo
ALTER TABLE `call_lang` ADD `dossier` tinytext;

-- Recursos de capital riego
ALTER TABLE `call_lang` ADD `resources` TEXT NULL DEFAULT NULL COMMENT 'Recursos de capital riego';

-- Texto para el tweet
ALTER TABLE `call_lang` ADD `tweet` tinytext DEFAULT NULL AFTER `dossier` ;

-- nombre
ALTER TABLE `call_lang` ADD `name` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `lang`;

-- pendiente de traducir
ALTER TABLE `call_lang` ADD `pending` INT( 1 ) NULL DEFAULT '0' COMMENT 'Debe revisarse la traducción';

-- Los textos pueden ser muy largos...
ALTER TABLE `call_lang` CHANGE `description` `description` LONGTEXT NULL;


DELETE FROM call_lang WHERE id NOT IN (SELECT id FROM `call`);
ALTER TABLE `call_lang` ADD FOREIGN KEY (`id`) REFERENCES `call`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
