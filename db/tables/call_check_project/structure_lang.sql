CREATE TABLE `call_check_lang` (
`id` int(10) unsigned NOT NULL,
`lang` varchar(2) NOT NULL,
`description` TEXT NOT NULL,
`pending` INT(1) NULL DEFAULT '0' COMMENT 'To be reviewed',
 UNIQUE KEY `id_lang` (`id`,`lang`),
 FOREIGN KEY (`id`) REFERENCES `call_check`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

