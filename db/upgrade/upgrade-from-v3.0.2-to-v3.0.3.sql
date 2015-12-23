/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

/* Foreign Keys must be dropped in the target to ensure that requires changes can be done*/

ALTER TABLE `invest`
    DROP FOREIGN KEY `invest_ibfk_1`  ,
    DROP FOREIGN KEY `invest_ibfk_2`  ;

/* Alter table in target */
ALTER TABLE `cost`
    ADD COLUMN `order` int(10) unsigned   NOT NULL DEFAULT 1 after `until` ,
    DROP KEY `id` ,
    ADD KEY `order`(`order`) ;

/* Alter table in target */
ALTER TABLE `invest`
    CHANGE `project` `project` varchar(50)  COLLATE utf8_general_ci NULL after `user` ;

/* Alter table in target */
ALTER TABLE `node`
    ADD COLUMN `home_img` varchar(255)  COLLATE utf8_general_ci NULL COMMENT 'Imagen para módulo canales en home' after `sponsors_limit` ;

/* Alter table in target */
ALTER TABLE `project_account`
    ADD CONSTRAINT `project_account_ibfk_1`
    FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;


/* Create table in target */
CREATE TABLE `purpose_copy`(
    `text` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `purpose` text COLLATE utf8_general_ci NOT NULL  ,
    `html` tinyint(1) NULL  COMMENT 'Si el texto lleva formato html' ,
    `group` varchar(50) COLLATE utf8_general_ci NOT NULL  DEFAULT 'general' COMMENT 'Agrupacion de uso' ,
    PRIMARY KEY (`text`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Explicación del propósito de los textos';


/* Alter table in target */
ALTER TABLE `reward`
    DROP KEY `id` ,
    ADD KEY `order`(`order`) ;

/* Alter table in target */
ALTER TABLE `stories`
    DROP FOREIGN KEY `stories_ibfk_2`  ;

/* Create table in target */
CREATE TABLE `user_donation`(
    `user` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `amount` int(11) NOT NULL  ,
    `name` varchar(255) COLLATE utf8_general_ci NULL  ,
    `surname` varchar(255) COLLATE utf8_general_ci NULL  COMMENT 'Apellido' ,
    `nif` varchar(12) COLLATE utf8_general_ci NULL  ,
    `address` tinytext COLLATE utf8_general_ci NULL  ,
    `zipcode` varchar(10) COLLATE utf8_general_ci NULL  ,
    `location` varchar(255) COLLATE utf8_general_ci NULL  ,
    `region` varchar(255) COLLATE utf8_general_ci NULL  COMMENT 'Provincia' ,
    `country` varchar(50) COLLATE utf8_general_ci NULL  ,
    `countryname` varchar(255) COLLATE utf8_general_ci NULL  COMMENT 'Nombre del pais' ,
    `numproj` int(2) NULL  DEFAULT 1 ,
    `year` varchar(4) COLLATE utf8_general_ci NOT NULL  ,
    `edited` int(1) NULL  DEFAULT 0 COMMENT 'Revisados por el usuario' ,
    `confirmed` int(1) NULL  DEFAULT 0 COMMENT 'Certificado generado' ,
    `pdf` varchar(255) COLLATE utf8_general_ci NULL  COMMENT 'nombre del archivo de certificado' ,
    PRIMARY KEY (`user`,`year`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Datos fiscales donativo';

ALTER TABLE `relief` ADD UNIQUE INDEX (`year`, `country`, `limit_amount`, `type`);

/* The foreign keys that were dropped are now re-created*/

ALTER TABLE `invest`
    ADD CONSTRAINT `invest_ibfk_1`
    FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON UPDATE CASCADE ,
    ADD CONSTRAINT `invest_ibfk_2`
    FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON UPDATE CASCADE ;

/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
