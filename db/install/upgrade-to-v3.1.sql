/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

/* Foreign Keys must be dropped in the target to ensure that requires changes can be done*/

ALTER TABLE `invest`
    DROP FOREIGN KEY `invest_ibfk_1`  ,
    DROP FOREIGN KEY `invest_ibfk_2`  ;

ALTER TABLE `invest_node`
    DROP FOREIGN KEY `invest_node_ibfk_1`  ,
    DROP FOREIGN KEY `invest_node_ibfk_2`  ,
    DROP FOREIGN KEY `invest_node_ibfk_3`  ,
    DROP FOREIGN KEY `invest_node_ibfk_4`  ,
    DROP FOREIGN KEY `invest_node_ibfk_5`  ,
    DROP FOREIGN KEY `invest_node_ibfk_6`  ;

ALTER TABLE `project_location`
    DROP FOREIGN KEY `project_location_ibfk_1`  ;

ALTER TABLE `user`
    DROP FOREIGN KEY `user_ibfk_1`  ;

ALTER TABLE `user_location`
    DROP FOREIGN KEY `user_location_ibfk_1`  ;


/* Create table in target */
CREATE TABLE `call`(
    `id` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `name` tinytext COLLATE utf8_general_ci NULL  ,
    `subtitle` text COLLATE utf8_general_ci NULL  ,
    `lang` varchar(2) COLLATE utf8_general_ci NOT NULL  DEFAULT 'es' ,
    `status` int(1) NOT NULL  ,
    `translate` int(1) NOT NULL  DEFAULT 0 ,
    `owner` varchar(50) COLLATE utf8_general_ci NOT NULL  COMMENT 'entidad que convoca' ,
    `amount` int(6) NOT NULL  COMMENT 'presupuesto' ,
    `created` date NULL  ,
    `updated` date NULL  ,
    `opened` date NULL  ,
    `published` date NULL  ,
    `success` date NULL  ,
    `closed` date NULL  ,
    `contract_name` varchar(255) COLLATE utf8_general_ci NULL  ,
    `contract_nif` varchar(10) COLLATE utf8_general_ci NULL  COMMENT 'Guardar sin espacios ni puntos ni guiones' ,
    `phone` varchar(20) COLLATE utf8_general_ci NULL  COMMENT 'guardar sin espacios ni puntos' ,
    `contract_email` varchar(255) COLLATE utf8_general_ci NULL  ,
    `address` tinytext COLLATE utf8_general_ci NULL  ,
    `zipcode` varchar(10) COLLATE utf8_general_ci NULL  ,
    `location` varchar(255) COLLATE utf8_general_ci NULL  ,
    `country` varchar(50) COLLATE utf8_general_ci NULL  ,
    `logo` varchar(255) COLLATE utf8_general_ci NULL  COMMENT 'Logo. Contiene nombre de archivo' ,
    `image` varchar(255) COLLATE utf8_general_ci NULL  COMMENT 'Imagen widget. Contiene nombre de archivo' ,
    `backimage` varchar(255) COLLATE utf8_general_ci NULL  COMMENT 'Imagen background. Contiene nombre de archivo' ,
    `description` longtext COLLATE utf8_general_ci NULL  ,
    `description_summary` text COLLATE utf8_general_ci NULL  ,
    `description_nav` text COLLATE utf8_general_ci NULL  ,
    `whom` text COLLATE utf8_general_ci NULL  ,
    `apply` text COLLATE utf8_general_ci NULL  ,
    `legal` longtext COLLATE utf8_general_ci NULL  ,
    `dossier` tinytext COLLATE utf8_general_ci NULL  ,
    `tweet` tinytext COLLATE utf8_general_ci NULL  ,
    `fbappid` tinytext COLLATE utf8_general_ci NULL  ,
    `call_location` varchar(256) COLLATE utf8_general_ci NULL  ,
    `resources` text COLLATE utf8_general_ci NULL  COMMENT 'Recursos de capital riego' ,
    `scope` int(1) NOT NULL  ,
    `contract_entity` int(1) NOT NULL  DEFAULT 0 ,
    `contract_birthdate` date NULL  ,
    `entity_office` varchar(255) COLLATE utf8_general_ci NULL  COMMENT 'Cargo del responsable' ,
    `entity_name` varchar(255) COLLATE utf8_general_ci NULL  ,
    `entity_cif` varchar(10) COLLATE utf8_general_ci NULL  COMMENT 'Guardar sin espacios ni puntos ni guiones' ,
    `post_address` tinytext COLLATE utf8_general_ci NULL  ,
    `secondary_address` int(11) NOT NULL  DEFAULT 0 ,
    `post_zipcode` varchar(10) COLLATE utf8_general_ci NULL  ,
    `post_location` varchar(255) COLLATE utf8_general_ci NULL  ,
    `post_country` varchar(50) COLLATE utf8_general_ci NULL  ,
    `days` int(2) NULL  ,
    `maxdrop` int(6) NULL  COMMENT 'Riego maximo por aporte' ,
    `modemaxp` varchar(3) COLLATE utf8_general_ci NULL  DEFAULT 'imp' COMMENT 'Modalidad del máximo por proyecto: imp = importe, per = porcentaje' ,
    `maxproj` int(6) NOT NULL  COMMENT 'Riego maximo por proyecto' ,
    `num_projects` int(10) unsigned NOT NULL  COMMENT 'Número de proyectos publicados' ,
    `rest` int(10) unsigned NOT NULL  COMMENT 'Importe riego disponible' ,
    `used` int(10) unsigned NOT NULL  COMMENT 'Importe riego comprometido' ,
    `applied` int(10) unsigned NOT NULL  COMMENT 'Número de proyectos aplicados' ,
    `running_projects` int(10) unsigned NOT NULL  COMMENT 'Número de proyectos en campaña' ,
    `success_projects` int(10) unsigned NOT NULL  COMMENT 'Número de proyectos exitosos' ,
    PRIMARY KEY (`id`) ,
    KEY `owner`(`owner`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Convocatorias';


/* Create table in target */
CREATE TABLE `call_banner`(
    `id` bigint(20) unsigned NOT NULL  auto_increment ,
    `call` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `name` tinytext COLLATE utf8_general_ci NOT NULL  ,
    `url` tinytext COLLATE utf8_general_ci NULL  ,
    `image` varchar(255) COLLATE utf8_general_ci NULL  COMMENT 'Contiene nombre de archivo' ,
    `order` int(11) NOT NULL  DEFAULT 1 ,
    PRIMARY KEY (`id`) ,
    UNIQUE KEY `id`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Banners de convocatorias';


/* Create table in target */
CREATE TABLE `call_banner_lang`(
    `id` int(20) NOT NULL  ,
    `lang` varchar(2) COLLATE utf8_general_ci NOT NULL  ,
    `name` tinytext COLLATE utf8_general_ci NULL  ,
    `pending` int(1) NULL  DEFAULT 0 COMMENT 'Debe revisarse la traducción' ,
    UNIQUE KEY `id_lang`(`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Create table in target */
CREATE TABLE `call_category`(
    `call` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `category` int(12) NOT NULL  ,
    UNIQUE KEY `call_category`(`call`,`category`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Categorias de las convocatorias';


/* Create table in target */
CREATE TABLE `call_conf`(
    `call` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `applied` int(4) NULL  COMMENT 'Para fijar numero de proyectos recibidos' ,
    `limit1` set('normal','minimum','unlimited','none') COLLATE utf8_general_ci NOT NULL  DEFAULT 'normal' COMMENT 'tipo limite riego primera ronda' ,
    `limit2` set('normal','minimum','unlimited','none') COLLATE utf8_general_ci NOT NULL  DEFAULT 'none' COMMENT 'tipo limite riego segunda ronda' ,
    `buzz_first` int(1) NOT NULL  DEFAULT 0 COMMENT 'Solo primer hashtag en el buzz' ,
    `buzz_own` int(1) NOT NULL  DEFAULT 1 COMMENT 'Tweets  propios en el buzz' ,
    `buzz_mention` int(1) NOT NULL  DEFAULT 1 COMMENT 'Menciones en el buzz' ,
    `map_stage1` varchar(256) COLLATE utf8_general_ci NULL  COMMENT 'Map iframe for stage 1' ,
    `map_stage2` varchar(256) COLLATE utf8_general_ci NULL  COMMENT 'Map iframe for stage 2' ,
    `date_stage1` date NULL  COMMENT 'Stage 1 date' ,
    `date_stage1_out` date NULL  COMMENT 'Stage 1 date out' ,
    `date_stage2` date NULL  COMMENT 'Stage 2 date' ,
    `date_stage3` date NULL  COMMENT 'Stage 3 date' ,
    PRIMARY KEY (`call`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Configuración de convocatoria';


/* Create table in target */
CREATE TABLE `call_icon`(
    `call` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `icon` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    UNIQUE KEY `call_icon`(`call`,`icon`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Tipos de retorno de las convocatorias';


/* Create table in target */
CREATE TABLE `call_lang`(
    `id` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `lang` varchar(2) COLLATE utf8_general_ci NOT NULL  ,
    `name` tinytext COLLATE utf8_general_ci NULL  ,
    `description` longtext COLLATE utf8_general_ci NULL  ,
    `description_summary` text COLLATE utf8_general_ci NULL  ,
    `description_nav` text COLLATE utf8_general_ci NULL  ,
    `whom` text COLLATE utf8_general_ci NULL  ,
    `apply` text COLLATE utf8_general_ci NULL  ,
    `legal` longtext COLLATE utf8_general_ci NULL  ,
    `subtitle` text COLLATE utf8_general_ci NULL  ,
    `dossier` tinytext COLLATE utf8_general_ci NULL  ,
    `tweet` tinytext COLLATE utf8_general_ci NULL  ,
    `resources` text COLLATE utf8_general_ci NULL  COMMENT 'Recursos de capital riego' ,
    `pending` int(1) NULL  DEFAULT 0 COMMENT 'Debe revisarse la traducción' ,
    UNIQUE KEY `id_lang`(`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Create table in target */
CREATE TABLE `call_location`(
    `id` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `latitude` decimal(16,14) NOT NULL  ,
    `longitude` decimal(16,14) NOT NULL  ,
    `radius` smallint(6) unsigned NOT NULL  DEFAULT 0 ,
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


/* Create table in target */
CREATE TABLE `call_post`(
    `call` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `post` int(20) NOT NULL  ,
    UNIQUE KEY `call_post`(`call`,`post`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Entradas de blog asignadas a convocatorias';


/* Create table in target */
CREATE TABLE `call_project`(
    `call` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `project` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    UNIQUE KEY `call_project`(`call`,`project`) ,
    KEY `call_project_ibfk_2`(`project`) ,
    CONSTRAINT `call_project_ibfk_1`
    FOREIGN KEY (`call`) REFERENCES `call` (`id`) ON UPDATE CASCADE ,
    CONSTRAINT `call_project_ibfk_2`
    FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Proyectos asignados a convocatorias';


/* Create table in target */
CREATE TABLE `call_sponsor`(
    `id` bigint(20) unsigned NOT NULL  auto_increment ,
    `call` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `name` tinytext COLLATE utf8_general_ci NOT NULL  ,
    `url` tinytext COLLATE utf8_general_ci NULL  ,
    `image` varchar(255) COLLATE utf8_general_ci NULL  COMMENT 'Contiene nombre de archivo' ,
    `order` int(11) NOT NULL  DEFAULT 1 ,
    `amount` int(11) NULL  ,
    PRIMARY KEY (`id`) ,
    UNIQUE KEY `id`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Patrocinadores de convocatorias';


/* Create table in target */
CREATE TABLE `campaign`(
    `id` bigint(20) unsigned NOT NULL  auto_increment ,
    `node` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `call` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `active` int(1) NOT NULL  DEFAULT 0 ,
    `order` smallint(5) unsigned NOT NULL  DEFAULT 1 ,
    PRIMARY KEY (`id`) ,
    UNIQUE KEY `id`(`id`) ,
    UNIQUE KEY `call_node`(`node`,`call`) ,
    CONSTRAINT `campaign_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Convocatorias en portada';


/* Create table in target */
CREATE TABLE `conf`(
    `key` varchar(255) COLLATE utf8_general_ci NOT NULL  COMMENT 'Clave' ,
    `value` varchar(255) COLLATE utf8_general_ci NOT NULL  COMMENT 'Valor'
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Para guardar pares para configuraciones, bloqueos etc';


/* Create table in target */
CREATE TABLE `contract`(
    `project` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `number` int(11) NOT NULL  auto_increment ,
    `date` date NOT NULL  COMMENT 'dia anterior a la publicacion' ,
    `enddate` date NOT NULL  COMMENT 'finalización, un año despues de la fecha de contrato' ,
    `pdf` varchar(255) COLLATE utf8_general_ci NULL  COMMENT 'Archivo pdf contrato' ,
    `type` varchar(1) COLLATE utf8_general_ci NOT NULL  DEFAULT '0' COMMENT '0 = persona física; 1 = representante asociacion; 2 = apoderado entidad mercantil' ,
    `name` tinytext COLLATE utf8_general_ci NULL  ,
    `nif` varchar(14) COLLATE utf8_general_ci NULL  ,
    `office` tinytext COLLATE utf8_general_ci NULL  COMMENT 'Cargo en la asociación o empresa' ,
    `address` tinytext COLLATE utf8_general_ci NULL  ,
    `location` varchar(255) COLLATE utf8_general_ci NULL  ,
    `region` varchar(255) COLLATE utf8_general_ci NULL  ,
    `zipcode` varchar(8) COLLATE utf8_general_ci NULL  ,
    `country` varchar(50) COLLATE utf8_general_ci NULL  ,
    `entity_name` tinytext COLLATE utf8_general_ci NULL  ,
    `entity_cif` varchar(10) COLLATE utf8_general_ci NULL  ,
    `entity_address` tinytext COLLATE utf8_general_ci NULL  ,
    `entity_location` varchar(255) COLLATE utf8_general_ci NULL  ,
    `entity_region` varchar(255) COLLATE utf8_general_ci NULL  ,
    `entity_zipcode` varchar(8) COLLATE utf8_general_ci NULL  ,
    `entity_country` varchar(50) COLLATE utf8_general_ci NULL  ,
    `reg_name` tinytext COLLATE utf8_general_ci NULL  COMMENT 'Nombre y ciudad del registro en el que esta inscrita la entidad' ,
    `reg_date` date NULL  ,
    `reg_number` tinytext COLLATE utf8_general_ci NULL  COMMENT 'Número de registro' ,
    `reg_loc` tinytext COLLATE utf8_general_ci NULL  COMMENT 'NO SE USA (borrar)' ,
    `reg_id` tinytext COLLATE utf8_general_ci NULL  COMMENT 'Número de protocolo del notario' ,
    `reg_idname` tinytext COLLATE utf8_general_ci NULL  COMMENT 'Nombre del notario' ,
    `reg_idloc` tinytext COLLATE utf8_general_ci NULL  COMMENT 'Ciudad de actuación del notario' ,
    `project_name` tinytext COLLATE utf8_general_ci NULL  COMMENT 'Nombre del proyecto' ,
    `project_url` varchar(255) COLLATE utf8_general_ci NULL  COMMENT 'URL del proyecto' ,
    `project_owner` tinytext COLLATE utf8_general_ci NULL  COMMENT 'Nombre del impulsor' ,
    `project_user` tinytext COLLATE utf8_general_ci NULL  COMMENT 'Nombre del usuario autor del proyecto' ,
    `project_profile` varchar(255) COLLATE utf8_general_ci NULL  COMMENT 'URL del perfil del autor del proyecto' ,
    `project_description` text COLLATE utf8_general_ci NULL  COMMENT 'Breve descripción del proyecto' ,
    `project_invest` text COLLATE utf8_general_ci NULL  COMMENT 'objetivo del crowdfunding' ,
    `project_return` text COLLATE utf8_general_ci NULL  COMMENT 'retornos' ,
    `bank` tinytext COLLATE utf8_general_ci NULL  ,
    `bank_owner` tinytext COLLATE utf8_general_ci NULL  ,
    `paypal` tinytext COLLATE utf8_general_ci NULL  ,
    `paypal_owner` tinytext COLLATE utf8_general_ci NULL  ,
    `birthdate` date NULL  ,
    PRIMARY KEY (`project`) ,
    UNIQUE KEY `numero`(`number`) ,
    CONSTRAINT `contract_ibfk_1`
    FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Contratos';


/* Create table in target */
CREATE TABLE `contract_status`(
    `contract` varchar(50) COLLATE utf8_general_ci NOT NULL  COMMENT 'Id del proyecto' ,
    `owner` int(1) NOT NULL  DEFAULT 0 COMMENT 'El impulsor ha dado por rellenados los datos' ,
    `owner_date` date NULL  COMMENT 'Fecha que se cambia el flag' ,
    `owner_user` varchar(50) COLLATE utf8_general_ci NULL  COMMENT 'Usuario que cambia el flag' ,
    `admin` int(1) NOT NULL  DEFAULT 0 COMMENT 'El admin ha comenzado a revisar los datos' ,
    `admin_date` date NULL  COMMENT 'Fecha que se cambia el flag' ,
    `admin_user` varchar(50) COLLATE utf8_general_ci NULL  COMMENT 'Usuario que cambia el flag' ,
    `ready` int(1) NOT NULL  DEFAULT 0 COMMENT 'Datos verificados y correctos' ,
    `ready_date` date NULL  COMMENT 'Fecha que se cambia el flag' ,
    `ready_user` varchar(50) COLLATE utf8_general_ci NULL  COMMENT 'Usuario que cambia el flag' ,
    `pdf` int(1) NOT NULL  COMMENT 'El impulsor ha descargado el pdf' ,
    `pdf_date` date NULL  COMMENT 'Fecha que se cambia el flag' ,
    `pdf_user` varchar(50) COLLATE utf8_general_ci NULL  COMMENT 'Usuario que cambia el flag' ,
    `recieved` int(1) NOT NULL  DEFAULT 0 COMMENT 'Se ha recibido el contrato firmado' ,
    `recieved_date` date NULL  COMMENT 'Fecha que se cambia el flag' ,
    `recieved_user` varchar(50) COLLATE utf8_general_ci NULL  COMMENT 'Usuario que cambia el flag' ,
    `payed` int(1) NOT NULL  DEFAULT 0 COMMENT 'Se ha realizado el pago al proyecto' ,
    `payed_date` date NULL  COMMENT 'Fecha que se cambia el flag' ,
    `payed_user` varchar(50) COLLATE utf8_general_ci NULL  COMMENT 'Usuario que cambia el flag' ,
    `prepay` int(1) NOT NULL  DEFAULT 0 COMMENT 'Ha habido pago avanzado' ,
    `prepay_date` date NULL  COMMENT 'Fecha que se cambia el flag' ,
    `prepay_user` varchar(50) COLLATE utf8_general_ci NULL  COMMENT 'Usuario que cambia el flag' ,
    `closed` int(1) NOT NULL  DEFAULT 0 COMMENT 'Contrato finiquitado' ,
    `closed_date` date NULL  COMMENT 'Fecha que se cambia el flag' ,
    `closed_user` varchar(50) COLLATE utf8_general_ci NULL  COMMENT 'Usuario que cambia el flag' ,
    PRIMARY KEY (`contract`) ,
    CONSTRAINT `contract_status_ibfk_1`
    FOREIGN KEY (`contract`) REFERENCES `contract` (`project`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Seguimiento de estado de contrato';


/* Alter table in target */
ALTER TABLE `cost`
    ADD COLUMN `order` int(10) unsigned   NOT NULL DEFAULT 1 after `until` ,
    DROP KEY `id` ,
    ADD KEY `order`(`order`) ;

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
    `radius` smallint(6) unsigned NOT NULL  DEFAULT 0 ,
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


/* Alter table in target */
ALTER TABLE `invest`
    CHANGE `project` `project` varchar(50)  COLLATE utf8_general_ci NULL after `user` ;

/* Create table in target */
CREATE TABLE `invest_location`(
    `id` bigint(20) unsigned NOT NULL  ,
    `latitude` decimal(16,14) NOT NULL  ,
    `longitude` decimal(16,14) NOT NULL  ,
    `radius` smallint(6) unsigned NOT NULL  DEFAULT 0 ,
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


/* Create table in target */
CREATE TABLE `invest_msg`(
    `invest` bigint(20) unsigned NOT NULL  ,
    `msg` text COLLATE utf8_general_ci NULL  ,
    PRIMARY KEY (`invest`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Mensaje de apoyo al proyecto tras aportar';


/* Alter table in target */
ALTER TABLE `invest_node`
    CHANGE `project_id` `project_id` varchar(50)  COLLATE utf8_general_ci NULL after `user_node` ,
    CHANGE `project_node` `project_node` varchar(50)  COLLATE utf8_general_ci NULL after `project_id` ;

/* Alter table in target */
ALTER TABLE `license`
    CHANGE `description` `description` text  COLLATE utf8_general_ci NULL after `name` ;

/* Alter table in target */
ALTER TABLE `license_lang`
    CHANGE `description` `description` text  COLLATE utf8_general_ci NULL after `name` ;

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


/* Alter table in target */
ALTER TABLE `node`
    ADD COLUMN `home_img` varchar(255)  COLLATE utf8_general_ci NULL COMMENT 'Imagen para módulo canales en home' after `sponsors_limit` ;

/* Create table in target */
CREATE TABLE `open_tag`(
    `id` bigint(20) unsigned NOT NULL  auto_increment ,
    `name` tinytext COLLATE utf8_general_ci NULL  ,
    `description` text COLLATE utf8_general_ci NULL  ,
    `order` tinyint(3) unsigned NOT NULL  DEFAULT 1 ,
    `post` bigint(20) unsigned NULL  ,
    PRIMARY KEY (`id`) ,
    UNIQUE KEY `id`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Agrupacion de los proyectos';


/* Create table in target */
CREATE TABLE `open_tag_lang`(
    `id` bigint(20) unsigned NOT NULL  ,
    `lang` varchar(2) COLLATE utf8_general_ci NOT NULL  ,
    `name` tinytext COLLATE utf8_general_ci NULL  ,
    `description` text COLLATE utf8_general_ci NULL  ,
    `pending` int(1) NULL  DEFAULT 0 COMMENT 'Debe revisarse la traducción' ,
    UNIQUE KEY `id_lang`(`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


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


/* Create table in target */
CREATE TABLE `patron`(
    `id` bigint(20) unsigned NOT NULL  auto_increment ,
    `node` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `project` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `user` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `title` tinytext COLLATE utf8_general_ci NULL  ,
    `description` text COLLATE utf8_general_ci NULL  ,
    `link` tinytext COLLATE utf8_general_ci NULL  ,
    `order` smallint(5) unsigned NOT NULL  DEFAULT 1 ,
    `active` int(1) NOT NULL  DEFAULT 0 ,
    PRIMARY KEY (`id`) ,
    UNIQUE KEY `user_project_node`(`node`,`project`,`user`) ,
    KEY `project`(`project`) ,
    CONSTRAINT `patron_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE ,
    CONSTRAINT `patron_ibfk_2`
    FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Proyectos recomendados por padrinos';


/* Create table in target */
CREATE TABLE `patron_lang`(
    `id` bigint(20) unsigned NOT NULL  ,
    `lang` varchar(2) COLLATE utf8_general_ci NOT NULL  ,
    `title` tinytext COLLATE utf8_general_ci NULL  ,
    `description` text COLLATE utf8_general_ci NULL  ,
    `pending` int(1) NULL  DEFAULT 0 COMMENT 'Debe revisarse la traducción' ,
    UNIQUE KEY `id_lang`(`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Create table in target */
CREATE TABLE `patron_order`(
    `id` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `order` tinyint(3) unsigned NOT NULL  DEFAULT 1 ,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Orden de los padrinos';


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


/* Create table in target */
CREATE TABLE `relief`(
    `id` bigint(20) unsigned NOT NULL  auto_increment ,
    `year` int(4) NOT NULL  ,
    `percentage` int(2) NOT NULL  ,
    `country` varchar(10) COLLATE utf8_general_ci NULL  ,
    `limit_amount` int(10) NOT NULL  ,
    `type` int(1) NOT NULL  ,
    PRIMARY KEY (`id`) ,
    UNIQUE KEY `year`(`year`,`country`,`limit_amount`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Desgravaciones fiscales';


/* Alter table in target */
ALTER TABLE `reward`
    DROP KEY `id` ,
    ADD KEY `order`(`order`) ;

/* Alter table in target */
ALTER TABLE `stories`
    ADD COLUMN `pool_image` varchar(255)  COLLATE utf8_general_ci NULL after `post` ,
    ADD COLUMN `pool` int(1)   NOT NULL DEFAULT 0 after `pool_image` ,
    ADD COLUMN `text_position` varchar(50)  COLLATE utf8_general_ci NULL after `pool` ;

/* Alter table in target */
ALTER TABLE `template`
    ADD COLUMN `type` char(20)  COLLATE utf8_general_ci NOT NULL DEFAULT 'html' after `text` ;

/* Alter table in target */
ALTER TABLE `user`
    CHANGE `password` `password` varchar(255)  COLLATE utf8_general_ci NOT NULL after `email` ,
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

/* Create table in target */
CREATE TABLE `user_call`(
    `user` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `call` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    PRIMARY KEY (`user`,`call`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Asignacion de convocatorias a admines';


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

ALTER TABLE `invest`
    ADD CONSTRAINT `invest_ibfk_1`
    FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON UPDATE CASCADE ,
    ADD CONSTRAINT `invest_ibfk_2`
    FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON UPDATE CASCADE ;

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

ALTER TABLE `project_location`
    ADD CONSTRAINT `project_location_ibfk_1`
    FOREIGN KEY (`id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `user`
    ADD CONSTRAINT `user_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE ;

ALTER TABLE `user_location`
    ADD CONSTRAINT `user_location_ibfk_1`
    FOREIGN KEY (`id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;

DROP TABLE `page_node`;

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

-- Fee to apply in the financial report to the drop
ALTER TABLE `call` ADD `fee_projects_drop` INT(2) NOT NULL DEFAULT 4 COMMENT 'Fee to apply in the financial report to the drop';

-- Adding order on gallery tables
ALTER TABLE `post_image` ADD COLUMN `order` TINYINT(4) DEFAULT 1 NOT NULL AFTER `image`;
ALTER TABLE `glossary_image` ADD COLUMN `order` TINYINT(4) DEFAULT 1 NOT NULL AFTER `image`;
ALTER TABLE `info_image` ADD COLUMN `order` TINYINT(4) DEFAULT 1 NOT NULL AFTER `image`;


/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;

