CREATE TABLE IF NOT EXISTS `reward_lang` (
`id` INT(20) NOT NULL,
`lang` varchar(2) NOT NULL,
`reward` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
`description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci ,
 UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

-- alter
ALTER TABLE `reward_lang` ADD `other` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci;

-- pendiente de traducir
ALTER TABLE `reward_lang` ADD `pending` INT( 1 ) NULL DEFAULT '0' COMMENT 'Debe revisarse la traducción';

-- indice proyecto
ALTER TABLE `reward_lang` ADD `project` VARCHAR( 50 ) NOT NULL AFTER `id` ,
ADD INDEX ( `project` );

-- rellenar este campo
UPDATE reward_lang SET reward_lang.project = (SELECT reward.project FROM reward WHERE reward.id = reward_lang.id);