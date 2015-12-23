/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

/* Foreign Keys must be dropped in the target to ensure that requires changes can be done*/


/* Drop in Second database */
DROP TABLE `acl`;

/* Drop in Second database */
DROP TABLE `user_image`;

ALTER TABLE `invest_node`
    DROP FOREIGN KEY `invest_node_ibfk_1`  ,
    DROP FOREIGN KEY `invest_node_ibfk_2`  ,
    DROP FOREIGN KEY `invest_node_ibfk_3`  ,
    DROP FOREIGN KEY `invest_node_ibfk_4`  ,
    DROP FOREIGN KEY `invest_node_ibfk_5`  ,
    DROP FOREIGN KEY `invest_node_ibfk_6`  ;


/* Create table in target */
CREATE TABLE `donor`(
    `id` bigint(20) unsigned NOT NULL  auto_increment ,
    `user` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `amount` int(11) NOT NULL  ,
    `name` varchar(255) COLLATE utf8_general_ci NULL  ,
    `surname` varchar(255) COLLATE utf8_general_ci NULL  COMMENT 'Apellido' ,
    `surname2` char(255) COLLATE utf8_general_ci NULL  ,
    `nif` varchar(12) COLLATE utf8_general_ci NULL  ,
    `address` tinytext COLLATE utf8_general_ci NULL  ,
    `zipcode` varchar(10) COLLATE utf8_general_ci NULL  ,
    `location` varchar(255) COLLATE utf8_general_ci NULL  ,
    `region` varchar(255) COLLATE utf8_general_ci NULL  COMMENT 'Provincia' ,
    `country` varchar(50) COLLATE utf8_general_ci NULL  ,
    `countryname` varchar(255) COLLATE utf8_general_ci NULL  COMMENT 'Nombre del pais' ,
    `gender` char(1) COLLATE utf8_general_ci NULL  ,
    `birthyear` year(4) NULL  ,
    `numproj` int(2) NULL  DEFAULT 1 ,
    `year` varchar(4) COLLATE utf8_general_ci NOT NULL  ,
    `edited` int(1) NULL  DEFAULT 0 COMMENT 'Revisados por el usuario' ,
    `confirmed` int(1) NULL  DEFAULT 0 COMMENT 'Certificado generado' ,
    `pdf` varchar(255) COLLATE utf8_general_ci NULL  COMMENT 'nombre del archivo de certificado' ,
    `created` datetime NULL  ,
    `modified` datetime NOT NULL  ,
    PRIMARY KEY (`id`) ,
    KEY `user`(`user`) ,
    KEY `year`(`year`) ,
    CONSTRAINT `donor_ibfk_1`
    FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Datos fiscales donativo';


/* Create table in target */
CREATE TABLE `donor_invest`(
    `donor_id` bigint(20) unsigned NOT NULL  ,
    `invest_id` bigint(20) unsigned NOT NULL  ,
    PRIMARY KEY (`donor_id`,`invest_id`) ,
    KEY `invest_id`(`invest_id`) ,
    CONSTRAINT `donor_invest_ibfk_1`
    FOREIGN KEY (`donor_id`) REFERENCES `donor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ,
    CONSTRAINT `donor_invest_ibfk_2`
    FOREIGN KEY (`invest_id`) REFERENCES `invest` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Create table in target */
CREATE TABLE `donor_location`(
    `id` bigint(20) unsigned NOT NULL  ,
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
    KEY `locable`(`locable`) ,
    CONSTRAINT `donor_location_ibfk_1`
    FOREIGN KEY (`id`) REFERENCES `donor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Create table in target */
CREATE TABLE `invest_location`(
    `id` bigint(20) unsigned NOT NULL  ,
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
    KEY `locable`(`locable`) ,
    CONSTRAINT `invest_location_ibfk_1`
    FOREIGN KEY (`id`) REFERENCES `invest` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Alter table in target */
ALTER TABLE `invest_node`
    CHANGE `project_id` `project_id` varchar(50)  COLLATE utf8_general_ci NULL after `user_node` ,
    CHANGE `project_node` `project_node` varchar(50)  COLLATE utf8_general_ci NULL after `project_id` ;

/* Create table in target */
CREATE TABLE `relief`(
    `id` bigint(20) unsigned NOT NULL  auto_increment ,
    `year` int(4) NOT NULL  ,
    `percentage` int(2) NOT NULL  ,
    `country` varchar(10) COLLATE utf8_general_ci NULL  ,
    `limit_amount` int(10) NOT NULL  ,
    `type` int(1) NOT NULL  ,
    PRIMARY KEY (`id`) ,
    UNIQUE KEY `id`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Desgravaciones fiscales';

/* Alter table in target */
ALTER TABLE `stories`
    ADD COLUMN `pool_image` varchar(255)  COLLATE utf8_general_ci NULL after `post` ,
    ADD COLUMN `pool` int(1)   NOT NULL DEFAULT 0 after `pool_image` ,
    ADD COLUMN `text_position` varchar(50)  COLLATE utf8_general_ci NULL after `pool` ;
ALTER TABLE `stories`
    ADD CONSTRAINT `stories_ibfk_2`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE ;

/* Drop in Second database */
DROP TABLE `user_donation`;

/* The foreign keys that were dropped are now re-created*/

ALTER TABLE `invest_node`
    ADD CONSTRAINT `invest_node_ibfk_1`
    FOREIGN KEY (`user_node`) REFERENCES `node` (`id`) ON UPDATE CASCADE ,
    ADD CONSTRAINT `invest_node_ibfk_2`
    FOREIGN KEY (`project_node`) REFERENCES `node` (`id`) ON UPDATE CASCADE ,
    ADD CONSTRAINT `invest_node_ibfk_3`
    FOREIGN KEY (`invest_node`) REFERENCES `node` (`id`) ON UPDATE CASCADE ,
    ADD CONSTRAINT `invest_node_ibfk_4`
    FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ,
    ADD CONSTRAINT `invest_node_ibfk_5`
    FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ,
    ADD CONSTRAINT `invest_node_ibfk_6`
    FOREIGN KEY (`invest_id`) REFERENCES `invest` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;

/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
