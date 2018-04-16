CREATE TABLE IF NOT EXISTS `category_lang` (
`id` bigint(20) unsigned NOT NULL,
`lang` varchar(2) NOT NULL,
`name` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
 UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

-- pendiente de traducir
ALTER TABLE `category_lang` ADD `pending` INT( 1 ) NULL DEFAULT '0' COMMENT 'Debe revisarse la traducción';

-- Optimization
ALTER TABLE `category_lang`
    DROP KEY `id_lang` ,
    ADD KEY `lang`(`lang`) ,
    ADD PRIMARY KEY(`id`,`lang`) ;


ALTER TABLE `category_lang` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL, ADD FOREIGN KEY (`id`) REFERENCES `category`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
