CREATE TABLE IF NOT EXISTS `message_lang` (
`id` INT(20) NOT NULL,
`lang` varchar(2) NOT NULL,
`message` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci ,
 UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

-- pendiente de traducir
ALTER TABLE `message_lang` ADD `pending` INT( 1 ) NULL DEFAULT '0' COMMENT 'Debe revisarse la traducción';


-- foreign indexs
DELETE FROM message_lang WHERE id NOT IN (SELECT id FROM message);
ALTER TABLE `message_lang` CHANGE `id` `id` BIGINT(20) UNSIGNED NOT NULL, ADD FOREIGN KEY (`id`) REFERENCES `message`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
