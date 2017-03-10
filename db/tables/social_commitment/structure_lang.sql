CREATE TABLE `social_commitment_lang` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
`lang` varchar(2) NOT NULL,
`name` CHAR(255) NOT NULL,
`description` TEXT NOT NULL,
`pending` INT(1) NULL DEFAULT '0' COMMENT 'To be reviewed',
 UNIQUE KEY `id_lang` (`id`,`lang`),
 FOREIGN KEY (`id`) REFERENCES `social_commitment`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

