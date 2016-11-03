/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

/* Foreign Keys must be dropped in the target to ensure that requires changes can be done*/

ALTER TABLE `call_location`
    DROP FOREIGN KEY `call_location_ibfk_1`  ;

ALTER TABLE `donor_location`
    DROP FOREIGN KEY `donor_location_ibfk_1`  ;

ALTER TABLE `invest_location`
    DROP FOREIGN KEY `invest_location_ibfk_1`  ;

ALTER TABLE `project_location`
    DROP FOREIGN KEY `project_location_ibfk_1`  ;

ALTER TABLE `user`
    DROP FOREIGN KEY `user_ibfk_1`  ;

ALTER TABLE `user_location`
    DROP FOREIGN KEY `user_location_ibfk_1`  ;


/* Alter table in target */
ALTER TABLE `call`
    CHANGE `subtitle` `subtitle` text  COLLATE utf8_general_ci NULL after `name` ,
    ADD COLUMN `description_summary` text  COLLATE utf8_general_ci NULL after `description` ,
    ADD COLUMN `description_nav` text  COLLATE utf8_general_ci NULL after `description_summary` ,
    CHANGE `whom` `whom` text  COLLATE utf8_general_ci NULL after `description_nav` ,
    CHANGE `apply` `apply` text  COLLATE utf8_general_ci NULL after `whom` ,
    CHANGE `legal` `legal` longtext  COLLATE utf8_general_ci NULL after `apply` ,
    CHANGE `dossier` `dossier` tinytext  COLLATE utf8_general_ci NULL after `legal` ,
    CHANGE `tweet` `tweet` tinytext  COLLATE utf8_general_ci NULL after `dossier` ,
    CHANGE `fbappid` `fbappid` tinytext  COLLATE utf8_general_ci NULL after `tweet` ,
    CHANGE `call_location` `call_location` varchar(256)  COLLATE utf8_general_ci NULL after `fbappid` ,
    CHANGE `resources` `resources` text  COLLATE utf8_general_ci NULL COMMENT 'Recursos de capital riego' after `call_location` ,
    CHANGE `scope` `scope` int(1)   NOT NULL after `resources` ,
    CHANGE `contract_entity` `contract_entity` int(1)   NOT NULL DEFAULT 0 after `scope` ,
    CHANGE `contract_birthdate` `contract_birthdate` date   NULL after `contract_entity` ,
    CHANGE `entity_office` `entity_office` varchar(255)  COLLATE utf8_general_ci NULL COMMENT 'Cargo del responsable' after `contract_birthdate` ,
    CHANGE `entity_name` `entity_name` varchar(255)  COLLATE utf8_general_ci NULL after `entity_office` ,
    CHANGE `entity_cif` `entity_cif` varchar(10)  COLLATE utf8_general_ci NULL COMMENT 'Guardar sin espacios ni puntos ni guiones' after `entity_name` ,
    CHANGE `post_address` `post_address` tinytext  COLLATE utf8_general_ci NULL after `entity_cif` ,
    CHANGE `secondary_address` `secondary_address` int(11)   NOT NULL DEFAULT 0 after `post_address` ,
    CHANGE `post_zipcode` `post_zipcode` varchar(10)  COLLATE utf8_general_ci NULL after `secondary_address` ,
    CHANGE `post_location` `post_location` varchar(255)  COLLATE utf8_general_ci NULL after `post_zipcode` ,
    CHANGE `post_country` `post_country` varchar(50)  COLLATE utf8_general_ci NULL after `post_location` ,
    CHANGE `days` `days` int(2)   NULL after `post_country` ,
    CHANGE `maxdrop` `maxdrop` int(6)   NULL COMMENT 'Riego maximo por aporte' after `days` ,
    CHANGE `modemaxp` `modemaxp` varchar(3)  COLLATE utf8_general_ci NULL DEFAULT 'imp' COMMENT 'Modalidad del máximo por proyecto: imp = importe, per = porcentaje' after `maxdrop` ,
    CHANGE `maxproj` `maxproj` int(6)   NOT NULL COMMENT 'Riego maximo por proyecto' after `modemaxp` ,
    CHANGE `num_projects` `num_projects` int(10) unsigned   NOT NULL COMMENT 'Número de proyectos publicados' after `maxproj` ,
    CHANGE `rest` `rest` int(10) unsigned   NOT NULL COMMENT 'Importe riego disponible' after `num_projects` ,
    CHANGE `used` `used` int(10) unsigned   NOT NULL COMMENT 'Importe riego comprometido' after `rest` ,
    CHANGE `applied` `applied` int(10) unsigned   NOT NULL COMMENT 'Número de proyectos aplicados' after `used` ,
    CHANGE `running_projects` `running_projects` int(10) unsigned   NOT NULL COMMENT 'Número de proyectos en campaña' after `applied` ,
    CHANGE `success_projects` `success_projects` int(10) unsigned   NOT NULL COMMENT 'Número de proyectos exitosos' after `running_projects` ;

/* Alter table in target */
ALTER TABLE `call_conf`
    ADD COLUMN `map_stage1` varchar(256)  COLLATE utf8_general_ci NULL COMMENT 'Map iframe for stage 1' after `buzz_mention` ,
    ADD COLUMN `map_stage2` varchar(256)  COLLATE utf8_general_ci NULL COMMENT 'Map iframe for stage 2' after `map_stage1` ,
    ADD COLUMN `date_stage1` date   NULL COMMENT 'Stage 1 date' after `map_stage2` ,
    ADD COLUMN `date_stage1_out` date   NULL COMMENT 'Stage 1 date out' after `date_stage1` ,
    ADD COLUMN `date_stage2` date   NULL COMMENT 'Stage 2 date' after `date_stage1_out` ,
    ADD COLUMN `date_stage3` date   NULL COMMENT 'Stage 3 date' after `date_stage2` ;

/* Alter table in target */
ALTER TABLE `call_lang`
    ADD COLUMN `description_summary` text  COLLATE utf8_general_ci NULL after `description` ,
    ADD COLUMN `description_nav` text  COLLATE utf8_general_ci NULL after `description_summary` ,
    CHANGE `whom` `whom` text  COLLATE utf8_general_ci NULL after `description_nav` ,
    CHANGE `apply` `apply` text  COLLATE utf8_general_ci NULL after `whom` ,
    CHANGE `legal` `legal` longtext  COLLATE utf8_general_ci NULL after `apply` ,
    CHANGE `subtitle` `subtitle` text  COLLATE utf8_general_ci NULL after `legal` ,
    CHANGE `dossier` `dossier` tinytext  COLLATE utf8_general_ci NULL after `subtitle` ,
    CHANGE `tweet` `tweet` tinytext  COLLATE utf8_general_ci NULL after `dossier` ,
    CHANGE `resources` `resources` text  COLLATE utf8_general_ci NULL COMMENT 'Recursos de capital riego' after `tweet` ,
    CHANGE `pending` `pending` int(1)   NULL DEFAULT 0 COMMENT 'Debe revisarse la traducción' after `resources` ;

/* Alter table in target */
ALTER TABLE `call_location`
    ADD COLUMN `radius` smallint(6) unsigned   NOT NULL DEFAULT 0 after `longitude` ,
    CHANGE `method` `method` varchar(50)  COLLATE utf8_general_ci NOT NULL DEFAULT 'ip' after `radius` ,
    CHANGE `locable` `locable` tinyint(1)   NOT NULL DEFAULT 0 after `method` ,
    CHANGE `city` `city` varchar(255)  COLLATE utf8_general_ci NOT NULL after `locable` ,
    CHANGE `region` `region` varchar(255)  COLLATE utf8_general_ci NOT NULL after `city` ,
    CHANGE `country` `country` varchar(150)  COLLATE utf8_general_ci NOT NULL after `region` ,
    CHANGE `country_code` `country_code` varchar(2)  COLLATE utf8_general_ci NOT NULL after `country` ,
    CHANGE `info` `info` varchar(255)  COLLATE utf8_general_ci NULL after `country_code` ,
    CHANGE `modified` `modified` timestamp   NOT NULL DEFAULT CURRENT_TIMESTAMP  on update CURRENT_TIMESTAMP after `info` ;

/* Alter table in target */
ALTER TABLE `call_sponsor`
    ADD COLUMN `amount` int(11)   NULL after `order` ;

/* Alter table in target */
ALTER TABLE `donor_location`
    ADD COLUMN `radius` smallint(6) unsigned   NOT NULL DEFAULT 0 after `longitude` ,
    CHANGE `method` `method` varchar(50)  COLLATE utf8_general_ci NOT NULL DEFAULT 'ip' after `radius` ,
    CHANGE `locable` `locable` tinyint(1)   NOT NULL DEFAULT 0 after `method` ,
    CHANGE `city` `city` varchar(255)  COLLATE utf8_general_ci NOT NULL after `locable` ,
    CHANGE `region` `region` varchar(255)  COLLATE utf8_general_ci NOT NULL after `city` ,
    CHANGE `country` `country` varchar(150)  COLLATE utf8_general_ci NOT NULL after `region` ,
    CHANGE `country_code` `country_code` varchar(2)  COLLATE utf8_general_ci NOT NULL after `country` ,
    CHANGE `info` `info` varchar(255)  COLLATE utf8_general_ci NULL after `country_code` ,
    CHANGE `modified` `modified` timestamp   NOT NULL DEFAULT CURRENT_TIMESTAMP  on update CURRENT_TIMESTAMP after `info` ;

/* Alter table in target */
ALTER TABLE `invest_location`
    ADD COLUMN `radius` smallint(6) unsigned   NOT NULL DEFAULT 0 after `longitude` ,
    CHANGE `method` `method` varchar(50)  COLLATE utf8_general_ci NOT NULL DEFAULT 'ip' after `radius` ,
    CHANGE `locable` `locable` tinyint(1)   NOT NULL DEFAULT 0 after `method` ,
    CHANGE `city` `city` varchar(255)  COLLATE utf8_general_ci NOT NULL after `locable` ,
    CHANGE `region` `region` varchar(255)  COLLATE utf8_general_ci NOT NULL after `city` ,
    CHANGE `country` `country` varchar(150)  COLLATE utf8_general_ci NOT NULL after `region` ,
    CHANGE `country_code` `country_code` varchar(2)  COLLATE utf8_general_ci NOT NULL after `country` ,
    CHANGE `info` `info` varchar(255)  COLLATE utf8_general_ci NULL after `country_code` ,
    CHANGE `modified` `modified` timestamp   NOT NULL DEFAULT CURRENT_TIMESTAMP  on update CURRENT_TIMESTAMP after `info` ;

/* Alter table in target */
ALTER TABLE `mail_stats`
    DROP FOREIGN KEY `mail_stats_ibfk_1`  ;

/* Alter table in target */
ALTER TABLE `mail_stats_location`
    ADD COLUMN `radius` smallint(6) unsigned   NOT NULL DEFAULT 0 after `longitude` ,
    CHANGE `method` `method` varchar(50)  COLLATE utf8_general_ci NOT NULL DEFAULT 'ip' after `radius` ,
    CHANGE `locable` `locable` tinyint(1)   NOT NULL DEFAULT 0 after `method` ,
    CHANGE `city` `city` varchar(255)  COLLATE utf8_general_ci NOT NULL after `locable` ,
    CHANGE `region` `region` varchar(255)  COLLATE utf8_general_ci NOT NULL after `city` ,
    CHANGE `country` `country` varchar(150)  COLLATE utf8_general_ci NOT NULL after `region` ,
    CHANGE `country_code` `country_code` varchar(2)  COLLATE utf8_general_ci NOT NULL after `country` ,
    CHANGE `info` `info` varchar(255)  COLLATE utf8_general_ci NULL after `country_code` ,
    CHANGE `modified` `modified` timestamp   NOT NULL DEFAULT CURRENT_TIMESTAMP  on update CURRENT_TIMESTAMP after `info` ,
    DROP FOREIGN KEY `mail_stats_location_ibfk_1`  ;

/* Alter table in target */
ALTER TABLE `page`
    ADD COLUMN `type` char(20)  COLLATE utf8_general_ci NOT NULL DEFAULT 'html' after `description` ,
    CHANGE `url` `url` tinytext  COLLATE utf8_general_ci NULL after `type` ,
    ADD COLUMN `content` longtext  COLLATE utf8_general_ci NULL after `url` ;

/* Alter table in target */
ALTER TABLE `page_lang`
    ADD COLUMN `content` longtext  COLLATE utf8_general_ci NULL after `description` ,
    ADD COLUMN `pending` tinyint(1)   NULL after `content` ;
ALTER TABLE `page_lang`
    ADD CONSTRAINT `page_lang_ibfk_1`
    FOREIGN KEY (`id`) REFERENCES `page` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;


/* Alter table in target */
ALTER TABLE `project_account`
    ADD COLUMN `vat` int(2)   NOT NULL DEFAULT 21 COMMENT '(Value Added Tax) to apply in the financial report' after `fee` ;

/* Alter table in target */
ALTER TABLE `project_location`
    ADD COLUMN `radius` smallint(6)   NOT NULL DEFAULT 0 after `longitude` ,
    CHANGE `method` `method` varchar(50)  COLLATE utf8_general_ci NOT NULL DEFAULT 'ip' after `radius` ,
    CHANGE `locable` `locable` tinyint(1)   NOT NULL DEFAULT 0 after `method` ,
    CHANGE `city` `city` varchar(255)  COLLATE utf8_general_ci NOT NULL after `locable` ,
    CHANGE `region` `region` varchar(255)  COLLATE utf8_general_ci NOT NULL after `city` ,
    CHANGE `country` `country` varchar(150)  COLLATE utf8_general_ci NOT NULL after `region` ,
    CHANGE `country_code` `country_code` varchar(2)  COLLATE utf8_general_ci NOT NULL after `country` ,
    CHANGE `info` `info` varchar(255)  COLLATE utf8_general_ci NULL after `country_code` ,
    CHANGE `modified` `modified` timestamp   NOT NULL DEFAULT CURRENT_TIMESTAMP  on update CURRENT_TIMESTAMP after `info` ;

/* Alter table in target */
ALTER TABLE `template`
    ADD COLUMN `type` char(20)  COLLATE utf8_general_ci NOT NULL DEFAULT 'html' after `text` ;

/* Alter table in target */
ALTER TABLE `user`
    ADD COLUMN `instagram` tinytext  COLLATE utf8_general_ci NULL after `google` ,
    CHANGE `identica` `identica` tinytext  COLLATE utf8_general_ci NULL after `instagram` ,
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

/* Alter table in target */
ALTER TABLE `user_location`
    ADD COLUMN `radius` smallint(6) unsigned   NOT NULL DEFAULT 0 after `longitude` ,
    CHANGE `method` `method` varchar(50)  COLLATE utf8_general_ci NOT NULL DEFAULT 'ip' after `radius` ,
    CHANGE `locable` `locable` tinyint(1)   NOT NULL DEFAULT 0 after `method` ,
    CHANGE `city` `city` varchar(255)  COLLATE utf8_general_ci NOT NULL after `locable` ,
    CHANGE `region` `region` varchar(255)  COLLATE utf8_general_ci NOT NULL after `city` ,
    CHANGE `country` `country` varchar(150)  COLLATE utf8_general_ci NOT NULL after `region` ,
    CHANGE `country_code` `country_code` varchar(2)  COLLATE utf8_general_ci NOT NULL after `country` ,
    CHANGE `info` `info` varchar(255)  COLLATE utf8_general_ci NULL after `country_code` ,
    CHANGE `modified` `modified` timestamp   NOT NULL DEFAULT CURRENT_TIMESTAMP  on update CURRENT_TIMESTAMP after `info` ;

/* The foreign keys that were dropped are now re-created*/

ALTER TABLE `call_location`
    ADD CONSTRAINT `call_location_ibfk_1`
    FOREIGN KEY (`id`) REFERENCES `call` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `donor_location`
    ADD CONSTRAINT `donor_location_ibfk_1`
    FOREIGN KEY (`id`) REFERENCES `donor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `invest_location`
    ADD CONSTRAINT `invest_location_ibfk_1`
    FOREIGN KEY (`id`) REFERENCES `invest` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `project_location`
    ADD CONSTRAINT `project_location_ibfk_1`
    FOREIGN KEY (`id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `user`
    ADD CONSTRAINT `user_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE ;

ALTER TABLE `user_location`
    ADD CONSTRAINT `user_location_ibfk_1`
    FOREIGN KEY (`id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;

/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
