/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

/* Foreign Keys must be dropped in the target to ensure that requires changes can be done*/

ALTER TABLE `contract`
    DROP FOREIGN KEY `contract_ibfk_1`  ;

ALTER TABLE `user`
    DROP FOREIGN KEY `user_ibfk_1`  ;


/* Alter table in target */
ALTER TABLE `call`
    CHANGE `amount` `amount` int(6)   NOT NULL COMMENT 'presupuesto' after `owner` ,
    CHANGE `scope` `scope` int(1)   NOT NULL after `resources` ,
    CHANGE `maxproj` `maxproj` int(6)   NOT NULL COMMENT 'Riego maximo por proyecto' after `modemaxp` ,
    CHANGE `num_projects` `num_projects` int(10) unsigned   NOT NULL COMMENT 'Número de proyectos publicados' after `maxproj` ,
    CHANGE `rest` `rest` int(10) unsigned   NOT NULL COMMENT 'Importe riego disponible' after `num_projects` ,
    CHANGE `used` `used` int(10) unsigned   NOT NULL COMMENT 'Importe riego comprometido' after `rest` ,
    CHANGE `applied` `applied` int(10) unsigned   NOT NULL COMMENT 'Número de proyectos aplicados' after `used` ,
    CHANGE `running_projects` `running_projects` int(10) unsigned   NOT NULL COMMENT 'Número de proyectos en campaña' after `applied` ,
    CHANGE `success_projects` `success_projects` int(10) unsigned   NOT NULL COMMENT 'Número de proyectos exitosos' after `running_projects` ;

/* Create table in target */
CREATE TABLE `call_location`(
    `id` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `latitude` decimal(16,14) NOT NULL  ,
    `longitude` decimal(16,14) NOT NULL  ,
    `method` varchar(50) COLLATE utf8_general_ci NOT NULL  DEFAULT 'ip' ,
    `locable` tinyint(1) NOT NULL  DEFAULT 0 ,
    `city` varchar(255) COLLATE utf8_general_ci NOT NULL  ,
    `region` varchar(255) COLLATE utf8_general_ci NOT NULL  ,
    `country` varchar(150) COLLATE utf8_general_ci NOT NULL  ,
    `country_code` varchar(2) COLLATE utf8_general_ci NOT NULL  ,
    `info` varchar(255) COLLATE utf8_general_ci NULL  ,
    `modified` timestamp NOT NULL  DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP ,
    PRIMARY KEY (`id`) ,
    KEY `latitude`(`latitude`) ,
    KEY `longitude`(`longitude`) ,
    CONSTRAINT `call_location_ibfk_1`
    FOREIGN KEY (`id`) REFERENCES `call` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Alter table in target */
ALTER TABLE `contract`
    CHANGE `nif` `nif` varchar(14)  COLLATE utf8_general_ci NULL after `name` ;

/* Create table in target */
CREATE TABLE `event`(
    `id` bigint(20) unsigned NOT NULL  auto_increment ,
    `type` char(20) COLLATE utf8_general_ci NOT NULL  DEFAULT 'communication' ,
    `action` char(100) COLLATE utf8_general_ci NOT NULL  ,
    `hash` char(32) COLLATE utf8_general_ci NOT NULL  ,
    `result` char(255) COLLATE utf8_general_ci NULL  ,
    `created` datetime NOT NULL  ,
    `finalized` datetime NULL  ,
    `succeeded` tinyint(1) NULL  DEFAULT 0 ,
    `error` char(255) COLLATE utf8_general_ci NULL  ,
    `modified` timestamp NOT NULL  DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP ,
    PRIMARY KEY (`id`) ,
    KEY `hash`(`hash`) ,
    KEY `succeeded`(`succeeded`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Create table in target */
CREATE TABLE `invest_msg`(
    `invest` bigint(20) unsigned NOT NULL  ,
    `msg` text COLLATE utf8_general_ci NULL  ,
    PRIMARY KEY (`invest`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Mensaje de apoyo al proyecto tras aportar';


/* Drop in Second database */
DROP TABLE `lang`;


/* Alter table in target */
ALTER TABLE `license`
    CHANGE `description` `description` text  COLLATE utf8_general_ci NULL after `name` ;

/* Alter table in target */
ALTER TABLE `license_lang`
    CHANGE `description` `description` text  COLLATE utf8_general_ci NULL after `name` ;

/* Create table in target */
CREATE TABLE `milestone`(
    `id` bigint(20) unsigned NOT NULL  auto_increment ,
    `type` varchar(255) COLLATE utf8_general_ci NULL  ,
    `description` text COLLATE utf8_general_ci NULL  ,
    `image` varchar(255) COLLATE utf8_general_ci NULL  ,
    `image_emoji` varchar(255) COLLATE utf8_general_ci NULL  ,
    `twitter_msg` text COLLATE utf8_general_ci NULL  ,
    `facebook_msg` text COLLATE utf8_general_ci NULL  ,
    `twitter_msg_owner` text COLLATE utf8_general_ci NULL  ,
    `facebook_msg_owner` text COLLATE utf8_general_ci NULL  ,
    `link` varchar(255) COLLATE utf8_general_ci NULL  ,
    PRIMARY KEY (`id`) ,
    UNIQUE KEY `id`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Milestones';


/* Create table in target */
CREATE TABLE `milestone_lang`(
    `id` bigint(20) unsigned NOT NULL  ,
    `lang` varchar(2) COLLATE utf8_general_ci NOT NULL  ,
    `name` varchar(255) COLLATE utf8_general_ci NULL  ,
    `description` text COLLATE utf8_general_ci NULL  ,
    `twitter_msg` text COLLATE utf8_general_ci NULL  ,
    `facebook_msg` text COLLATE utf8_general_ci NULL  ,
    `twitter_msg_owner` text COLLATE utf8_general_ci NULL  ,
    `facebook_msg_owner` text COLLATE utf8_general_ci NULL  ,
    `pending` int(1) NULL  DEFAULT 0 ,
    UNIQUE KEY `id_lang`(`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Create table in target */
CREATE TABLE `project_milestone`(
    `id` bigint(20) unsigned NOT NULL  auto_increment ,
    `project` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `milestone` int(12) NULL  ,
    `date` date NULL  ,
    `post` int(12) NULL  ,
    PRIMARY KEY (`id`) ,
    UNIQUE KEY `id`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Project milestones';


/* Alter table in target */
ALTER TABLE `relief`
    DROP KEY `id` ;

/* Alter table in target */
ALTER TABLE `user`
    ADD COLUMN `gender` char(1)  COLLATE utf8_general_ci NULL after `password` ,
    ADD COLUMN `birthyear` year(4)   NULL after `gender` ,
    ADD COLUMN `entity_type` tinyint(1)   NULL after `birthyear` ,
    ADD COLUMN `legal_entity` tinyint(1)   NULL after `entity_type` ,
    CHANGE `about` `about` text  COLLATE utf8_general_ci NULL after `legal_entity` ,
    CHANGE `keywords` `keywords` tinytext  COLLATE utf8_general_ci NULL after `about` ,
    CHANGE `active` `active` tinyint(1)   NOT NULL after `keywords` ,
    CHANGE `avatar` `avatar` varchar(255)  COLLATE utf8_general_ci NULL COMMENT 'Contiene nombre de archivo' after `active` ,
    CHANGE `contribution` `contribution` text  COLLATE utf8_general_ci NULL after `avatar` ,
    CHANGE `twitter` `twitter` tinytext  COLLATE utf8_general_ci NULL after `contribution` ,
    CHANGE `facebook` `facebook` tinytext  COLLATE utf8_general_ci NULL after `twitter` ,
    CHANGE `google` `google` tinytext  COLLATE utf8_general_ci NULL after `facebook` ,
    CHANGE `identica` `identica` tinytext  COLLATE utf8_general_ci NULL after `google` ,
    CHANGE `linkedin` `linkedin` tinytext  COLLATE utf8_general_ci NULL after `identica` ,
    CHANGE `amount` `amount` int(7)   NULL COMMENT 'Cantidad total aportada' after `linkedin` ,
    CHANGE `num_patron` `num_patron` int(10) unsigned   NULL COMMENT 'Num. proyectos patronizados' after `amount` ,
    CHANGE `num_patron_active` `num_patron_active` int(10) unsigned   NULL COMMENT 'Num. proyectos patronizados activos' after `num_patron` ,
    CHANGE `worth` `worth` int(7)   NULL after `num_patron_active` ,
    CHANGE `created` `created` timestamp   NULL after `worth` ,
    CHANGE `modified` `modified` timestamp   NULL DEFAULT CURRENT_TIMESTAMP  on update CURRENT_TIMESTAMP after `created` ,
    CHANGE `token` `token` tinytext  COLLATE utf8_general_ci NOT NULL after `modified` ,
    CHANGE `hide` `hide` tinyint(1)   NOT NULL DEFAULT 0 COMMENT 'No se ve publicamente' after `token` ,
    CHANGE `confirmed` `confirmed` int(1)   NOT NULL DEFAULT 0 after `hide` ,
    CHANGE `lang` `lang` varchar(2)  COLLATE utf8_general_ci NULL DEFAULT 'es' after `confirmed` ,
    CHANGE `node` `node` varchar(50)  COLLATE utf8_general_ci NULL after `lang` ,
    CHANGE `num_invested` `num_invested` int(10) unsigned   NULL COMMENT 'Num. proyectos cofinanciados' after `node` ,
    CHANGE `num_owned` `num_owned` int(10) unsigned   NULL COMMENT 'Num. proyectos publicados' after `num_invested` ;

/* Create table in target */
CREATE TABLE `user_favourite_project`(
    `user` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `project` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `date_send` date NULL  ,
    `date_marked` date NULL  ,
    UNIQUE KEY `user_favourite_project`(`user`,`project`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='User favourites projects';


/* Alter table in target */
ALTER TABLE `user_lang`
    ADD COLUMN `name` varchar(100)  COLLATE utf8_general_ci NULL after `about` ,
    CHANGE `keywords` `keywords` tinytext  COLLATE utf8_general_ci NULL after `name` ,
    CHANGE `contribution` `contribution` text  COLLATE utf8_general_ci NULL after `keywords` ;

/* The foreign keys that were dropped are now re-created*/

ALTER TABLE `contract`
    ADD CONSTRAINT `contract_ibfk_1`
    FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON UPDATE CASCADE ;

ALTER TABLE `user`
    ADD CONSTRAINT `user_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE ;

/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
