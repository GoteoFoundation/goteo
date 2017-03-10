/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

/* Create table in target */
CREATE TABLE `call_sphere`(
    `call` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `sphere` int(12) NOT NULL  ,
    UNIQUE KEY `call_sphere`(`call`,`sphere`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Ámbito de convocatorias';


/* Alter table in target */
ALTER TABLE `category`
    ADD COLUMN `social_commitment` varchar(50)  COLLATE utf8_general_ci NULL COMMENT 'Social commitment' after `order` ;

/* Create table in target */
CREATE TABLE `lang`(
    `id` varchar(2) COLLATE utf8_general_ci NOT NULL  COMMENT 'Código ISO-639' ,
    `name` varchar(20) COLLATE utf8_general_ci NOT NULL  ,
    `active` int(1) NOT NULL  DEFAULT 0 ,
    `short` varchar(10) COLLATE utf8_general_ci NULL  ,
    `locale` varchar(5) COLLATE utf8_general_ci NULL  ,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Idiomas';


/* Alter table in target */
ALTER TABLE `project`
    ADD COLUMN `analytics_id` varchar(30)  COLLATE utf8_general_ci NULL after `maxproj` ,
    ADD COLUMN `facebook_pixel` varchar(20)  COLLATE utf8_general_ci NULL after `analytics_id` ,
    ADD COLUMN `social_commitment` varchar(50)  COLLATE utf8_general_ci NULL COMMENT 'Social commitment of the project' after `facebook_pixel` ,
    ADD COLUMN `social_commitment_description` text  COLLATE utf8_general_ci NULL COMMENT 'Social commitment of the project' after `social_commitment` ,
    ADD COLUMN `execution_plan` text  COLLATE utf8_general_ci NULL after `social_commitment_description` ,
    ADD COLUMN `sustainability_model` text  COLLATE utf8_general_ci NULL after `execution_plan` ,
    ADD COLUMN `execution_plan_url` tinytext  COLLATE utf8_general_ci NULL after `sustainability_model` ,
    ADD COLUMN `sustainability_model_url` tinytext  COLLATE utf8_general_ci NULL after `execution_plan_url` ;

/* Alter table in target */
ALTER TABLE `project_conf`
    ADD COLUMN `mincost_estimation` int(11)   NULL after `help_cost` ,
    ADD COLUMN `publishing_estimation` date   NULL after `mincost_estimation` ;

/* Alter table in target */
ALTER TABLE `project_lang`
    ADD COLUMN `social_commitment_description` text  COLLATE utf8_general_ci NULL COMMENT 'Social commitment of the project' after `pending` ;

/* Alter table in target */
ALTER TABLE `reward`
    ADD COLUMN `category` varchar(50)  COLLATE utf8_general_ci NULL COMMENT 'Category social impact' after `bonus` ;

/* Create table in target */
CREATE TABLE `social_commitment`(
    `id` int(10) unsigned NOT NULL  auto_increment ,
    `name` char(255) COLLATE utf8_general_ci NOT NULL  ,
    `description` text COLLATE utf8_general_ci NOT NULL  ,
    `image` char(255) COLLATE utf8_general_ci NULL  ,
    `modified` timestamp NOT NULL  DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP ,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Compromiso social';


/* Create table in target */
CREATE TABLE `social_commitment_lang`(
    `id` int(10) unsigned NOT NULL  auto_increment ,
    `lang` varchar(2) COLLATE utf8_general_ci NOT NULL  ,
    `name` char(255) COLLATE utf8_general_ci NOT NULL  ,
    `description` text COLLATE utf8_general_ci NOT NULL  ,
    `pending` int(1) NULL  DEFAULT 0 COMMENT 'To be reviewed' ,
    UNIQUE KEY `id_lang`(`id`,`lang`) ,
    CONSTRAINT `social_commitment_lang_ibfk_1`
    FOREIGN KEY (`id`) REFERENCES `social_commitment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Create table in target */
CREATE TABLE `sphere`(
    `id` bigint(20) unsigned NOT NULL  auto_increment ,
    `name` varchar(255) COLLATE utf8_general_ci NULL  ,
    `image` varchar(255) COLLATE utf8_general_ci NULL  ,
    PRIMARY KEY (`id`) ,
    UNIQUE KEY `id`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Ámbitos de convocatorias';


/* Create table in target */
CREATE TABLE `sphere_lang`(
    `id` bigint(20) unsigned NOT NULL  ,
    `lang` varchar(2) COLLATE utf8_general_ci NOT NULL  ,
    `name` text COLLATE utf8_general_ci NULL  ,
    `pending` int(1) NULL  DEFAULT 0 ,
    UNIQUE KEY `id_lang`(`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';



/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
