DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` varchar(50) COLLATE utf8_general_ci NOT NULL,
  `name` varchar(100) COLLATE utf8_general_ci NOT NULL,
  `location` varchar(100) COLLATE utf8_general_ci,
  `email` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `password` varchar(40) COLLATE utf8_general_ci NOT NULL,
  `about` text COLLATE utf8_general_ci,
  `keywords` tinytext COLLATE utf8_general_ci,
  `active` tinyint(1) NOT NULL,
  `avatar` int(11) DEFAULT NULL,
  `contribution` text COLLATE utf8_general_ci,
  `twitter`  tinytext COLLATE utf8_general_ci,
  `facebook` tinytext COLLATE utf8_general_ci,
  `identica` tinytext COLLATE utf8_general_ci,
  `linkedin` tinytext COLLATE utf8_general_ci,
  `worth` int(7) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `token` tinytext COLLATE utf8_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


-- alters
ALTER TABLE `user` ADD `google` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `facebook` ;
ALTER TABLE `user` ADD `hide` BOOLEAN NOT NULL DEFAULT '0';

ALTER TABLE `user` ADD `confirmed` INT( 1 ) NOT NULL DEFAULT '0';

-- idioma preferido
ALTER TABLE `user` ADD `lang` VARCHAR( 2 ) NULL DEFAULT NULL;

-- nodo donde se registró
ALTER TABLE `user` ADD `node` VARCHAR( 50 ) NULL DEFAULT NULL;

-- cantidad aportada en goteo
ALTER TABLE `user` ADD `amount` INT( 7 ) NULL DEFAULT NULL COMMENT 'Cantidad total aportada' AFTER `linkedin` ;

-- campos de contar patronizaciones
ALTER TABLE `user`
  ADD COLUMN `num_patron` int(10) unsigned   NOT NULL DEFAULT 0 COMMENT 'Num. proyectos patronizados' after `amount` ,
  ADD COLUMN `num_patron_active` int(10) unsigned   NOT NULL DEFAULT 0 COMMENT 'Num. proyectos patronizados activos' after `num_patron` ,
  CHANGE `worth` `worth` int(7)   NULL after `num_patron_active` ,
  CHANGE `created` `created` timestamp   NULL after `worth` ,
  CHANGE `modified` `modified` timestamp   NULL DEFAULT CURRENT_TIMESTAMP  on update CURRENT_TIMESTAMP after `created` ,
  CHANGE `token` `token` tinytext  COLLATE utf8_general_ci NOT NULL after `modified` ,
  CHANGE `hide` `hide` tinyint(1)   NOT NULL DEFAULT 0 COMMENT 'No se ve publicamente' after `token` ,
  CHANGE `confirmed` `confirmed` int(1)   NOT NULL DEFAULT 0 after `hide` ,
  CHANGE `lang` `lang` varchar(2)  COLLATE utf8_general_ci NULL DEFAULT 'es' after `confirmed` ,
  CHANGE `node` `node` varchar(50)  COLLATE utf8_general_ci NULL after `lang` ;

-- numero proyectos cofinanciados
ALTER TABLE `user`
  ADD COLUMN `num_invested` int(10) unsigned   NOT NULL DEFAULT 0 COMMENT 'Num. proyectos cofinanciados';

-- numero proyectos publicados
ALTER TABLE `user`
  ADD COLUMN `num_owned` int(10) unsigned   NOT NULL DEFAULT 0 COMMENT 'Num. proyectos publicados';

-- campo imagen a nombre archivo
ALTER TABLE `user` CHANGE `avatar` `avatar` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';


-- cambio en los campos calculados (null y null por defecto)
ALTER TABLE `user` CHANGE `num_patron` `num_patron` INT( 10 ) UNSIGNED NULL DEFAULT NULL COMMENT 'Num. proyectos patronizados',
CHANGE `num_patron_active` `num_patron_active` INT( 10 ) UNSIGNED NULL DEFAULT NULL COMMENT 'Num. proyectos patronizados activos',
CHANGE `num_invested` `num_invested` INT( 10 ) UNSIGNED NULL DEFAULT NULL COMMENT 'Num. proyectos cofinanciados',
CHANGE `num_owned` `num_owned` INT( 10 ) UNSIGNED NULL DEFAULT NULL COMMENT 'Num. proyectos publicados';

-- Constrains
ALTER TABLE `user` ADD FOREIGN KEY (`node`) REFERENCES `node`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT;

--  Password length for strong security
ALTER TABLE `user` CHANGE `password` `password` VARCHAR(255) NOT NULL;


-- Add gender and birthyear
ALTER TABLE `user` ADD COLUMN `gender` CHAR(1) NULL AFTER `password`, ADD COLUMN `birthyear` YEAR NULL AFTER `gender`;


-- Add entity type
ALTER TABLE `user` ADD COLUMN `entity_type` tinyint(1) NULL AFTER `birthyear`, ADD COLUMN `legal_entity` tinyint(1) NULL AFTER `entity_type`;
