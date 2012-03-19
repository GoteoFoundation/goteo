CREATE TABLE IF NOT EXISTS `reward_lang` (
`id` INT(20) NOT NULL,
`lang` varchar(2) NOT NULL,
`reward` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
`description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci ,
 UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

-- alter
ALTER TABLE `reward_lang` ADD `other` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci;
