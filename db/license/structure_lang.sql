CREATE TABLE IF NOT EXISTS `license_lang` (
`id` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`lang` varchar(2) NOT NULL,
`name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`description` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`url` VARCHAR(256) NULL,
 UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

-- pendiente de traducir
ALTER TABLE `license_lang` ADD `pending` INT( 1 ) NULL DEFAULT '0' COMMENT 'Debe revisarse la traducción';

-- Optimization
ALTER TABLE `license_lang`
    DROP KEY `id_lang` ,
    ADD KEY `lang`(`lang`) ,
    ADD PRIMARY KEY(`id`,`lang`) ;
