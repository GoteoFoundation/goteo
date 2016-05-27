/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

INSERT IGNORE INTO `node` (`id`, `name`, `email`, `url`, `active`) VALUES('goteo', 'Goteo Central', '', '', 1);

/* Alter table in target */
ALTER TABLE `banner`
    CHANGE `image` `image` varchar(255)  COLLATE utf8_general_ci NULL COMMENT 'Contiene nombre de archivo' after `order` ,
    ADD KEY `banner_ibfk_1`(`node`) ,
    ADD KEY `banner_ibfk_2`(`project`) ;
ALTER TABLE `banner`
    ADD CONSTRAINT `banner_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE ,
    ADD CONSTRAINT `banner_ibfk_2`
    FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON UPDATE CASCADE ;


/* Alter table in target */
ALTER TABLE `banner_lang`
    ADD COLUMN `pending` int(1)   NULL DEFAULT 0 COMMENT 'Debe revisarse la traducción' after `description` ;

/* Create table in target */
CREATE TABLE `call`(
    `id` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `name` tinytext COLLATE utf8_general_ci NULL  ,
    `subtitle` tinytext COLLATE utf8_general_ci NULL  ,
    `lang` varchar(2) COLLATE utf8_general_ci NOT NULL  DEFAULT 'es' ,
    `status` int(1) NOT NULL  ,
    `translate` int(1) NOT NULL  DEFAULT 0 ,
    `owner` varchar(50) COLLATE utf8_general_ci NOT NULL  COMMENT 'entidad que convoca' ,
    `amount` int(6) NULL  COMMENT 'presupuesto' ,
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
    `whom` text COLLATE utf8_general_ci NULL  ,
    `apply` text COLLATE utf8_general_ci NULL  ,
    `legal` longtext COLLATE utf8_general_ci NULL  ,
    `dossier` tinytext COLLATE utf8_general_ci NULL  ,
    `tweet` tinytext COLLATE utf8_general_ci NULL  ,
    `fbappid` tinytext COLLATE utf8_general_ci NULL  ,
    `call_location` varchar(256) COLLATE utf8_general_ci NULL  ,
    `resources` text COLLATE utf8_general_ci NULL  COMMENT 'Recursos de capital riego' ,
    `scope` int(1) NULL  ,
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
    `maxproj` int(6) NULL  COMMENT 'Riego maximo por proyecto' ,
    `num_projects` int(10) unsigned NULL  COMMENT 'Número de proyectos publicados' ,
    `rest` int(10) unsigned NULL  COMMENT 'Importe riego disponible' ,
    `used` int(10) unsigned NULL  COMMENT 'Importe riego comprometido' ,
    `applied` int(10) unsigned NULL  COMMENT 'Número de proyectos aplicados' ,
    `running_projects` int(10) unsigned NULL  COMMENT 'Número de proyectos en campaña' ,
    `success_projects` int(10) unsigned NULL  COMMENT 'Número de proyectos exitosos' ,
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
    PRIMARY KEY (`id`) ,
    UNIQUE KEY `id`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Patrocinadores de convocatorias';


/* Alter table in target */
ALTER TABLE `campaign`
    ADD COLUMN `node` varchar(50)  COLLATE utf8_general_ci NOT NULL after `id` ,
    ADD COLUMN `call` varchar(50)  COLLATE utf8_general_ci NOT NULL after `node` ,
    ADD COLUMN `active` int(1)   NOT NULL DEFAULT 0 after `call` ,
    ADD COLUMN `order` smallint(5) unsigned   NOT NULL DEFAULT 1 after `active` ,
    DROP COLUMN `name` ,
    DROP COLUMN `description` ,
    ADD UNIQUE KEY `call_node`(`node`,`call`) , COMMENT='Convocatorias en portada' ;
ALTER TABLE `campaign`
    ADD CONSTRAINT `campaign_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE ;


/* Alter table in target */
ALTER TABLE `category`
    CHANGE `id` `id` int(10) unsigned   NOT NULL auto_increment first ;

/* Alter table in target */
ALTER TABLE `category_lang`
    ADD COLUMN `pending` int(1)   NULL DEFAULT 0 COMMENT 'Debe revisarse la traducción' after `description` ,
    DROP KEY `id_lang` ,
    ADD KEY `lang`(`lang`) ,
    ADD PRIMARY KEY(`id`,`lang`) ;

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
    `nif` varchar(10) COLLATE utf8_general_ci NULL  ,
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
ALTER TABLE `cost_lang`
    ADD COLUMN `project` varchar(50)  COLLATE utf8_general_ci NOT NULL after `id` ,
    CHANGE `lang` `lang` varchar(2)  COLLATE utf8_general_ci NOT NULL after `project` ,
    CHANGE `cost` `cost` tinytext  COLLATE utf8_general_ci NULL after `lang` ,
    CHANGE `description` `description` text  COLLATE utf8_general_ci NULL after `cost` ,
    ADD COLUMN `pending` int(1)   NULL DEFAULT 0 COMMENT 'Debe revisarse la traducción' after `description` ,
    ADD KEY `project`(`project`) ;

/* Alter table in target */
ALTER TABLE `criteria_lang`
    ADD COLUMN `pending` int(1)   NULL DEFAULT 0 COMMENT 'Debe revisarse la traducción' after `description` ;

/* Create table in target */
CREATE TABLE `document`(
    `id` int(10) unsigned NOT NULL  auto_increment ,
    `contract` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `name` varchar(255) COLLATE utf8_general_ci NULL  ,
    `type` varchar(20) COLLATE utf8_general_ci NULL  ,
    `size` int(10) unsigned NULL  ,
    PRIMARY KEY (`id`) ,
    KEY `contract`(`contract`) ,
    CONSTRAINT `document_ibfk_1`
    FOREIGN KEY (`contract`) REFERENCES `contract` (`project`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Alter table in target */
ALTER TABLE `faq`
    ADD KEY `node`(`node`) ;
ALTER TABLE `faq`
    ADD CONSTRAINT `faq_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE ;


/* Alter table in target */
ALTER TABLE `faq_lang`
    ADD COLUMN `pending` int(1)   NULL DEFAULT 0 COMMENT 'Debe revisarse la traducción' after `description` ;

/* Alter table in target */
ALTER TABLE `feed`
    CHANGE `image` `image` varchar(255)  COLLATE utf8_general_ci NULL COMMENT 'Contiene nombre de archivo' after `html` ,
    ADD COLUMN `post` int(20) unsigned   NULL COMMENT 'Entrada de blog' after `target_id` ;

/* Alter table in target */
ALTER TABLE `glossary`
    ADD COLUMN `image` varchar(255)  COLLATE utf8_general_ci NULL COMMENT 'Imagen principal' after `legend` ;

/* Alter table in target */
ALTER TABLE `glossary_image`
    CHANGE `glossary` `glossary` bigint(20) unsigned   NOT NULL first ,
    CHANGE `image` `image` varchar(255)  COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Contiene nombre de archivo' after `glossary` ;
ALTER TABLE `glossary_image`
    ADD CONSTRAINT `glossary_image_ibfk_1`
    FOREIGN KEY (`glossary`) REFERENCES `glossary` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;


/* Alter table in target */
ALTER TABLE `glossary_lang`
    ADD COLUMN `pending` int(1)   NULL DEFAULT 0 COMMENT 'Debe revisarse la traducción' after `legend` ;

/* Alter table in target */
ALTER TABLE `home`
    CHANGE `type` `type` varchar(5)  COLLATE utf8_general_ci NOT NULL DEFAULT 'main' COMMENT 'lateral o central' after `item` ,
    ADD KEY `node`(`node`) ;
ALTER TABLE `home`
    ADD CONSTRAINT `home_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;


/* Alter table in target */
ALTER TABLE `icon_lang`
    ADD COLUMN `pending` int(1)   NULL DEFAULT 0 COMMENT 'Debe revisarse la traducción' after `description` ;

/* Alter table in target */
ALTER TABLE `info`
    ADD COLUMN `gallery` varchar(2000)  COLLATE utf8_general_ci NULL COMMENT 'Galería de imagenes' after `legend` ,
    ADD COLUMN `image` varchar(255)  COLLATE utf8_general_ci NULL COMMENT 'Imagen principal' after `gallery` ,
    ADD COLUMN `share_facebook` tinytext  COLLATE utf8_general_ci NULL after `image` ,
    ADD COLUMN `share_twitter` tinytext  COLLATE utf8_general_ci NULL after `share_facebook` ,
    ADD KEY `node`(`node`) ;
ALTER TABLE `info`
    ADD CONSTRAINT `info_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE ;


/* Alter table in target */
ALTER TABLE `info_image`
    CHANGE `info` `info` bigint(20) unsigned   NOT NULL first ,
    CHANGE `image` `image` varchar(255)  COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Contiene nombre de archivo' after `info` ;
ALTER TABLE `info_image`
    ADD CONSTRAINT `info_image_ibfk_1`
    FOREIGN KEY (`info`) REFERENCES `info` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;


/* Alter table in target */
ALTER TABLE `info_lang`
    ADD COLUMN `pending` int(1)   NULL DEFAULT 0 COMMENT 'Debe revisarse la traducción' after `legend` ,
    ADD COLUMN `share_facebook` tinytext  COLLATE utf8_general_ci NULL after `pending` ,
    ADD COLUMN `share_twitter` tinytext  COLLATE utf8_general_ci NULL after `share_facebook` ;

/* Alter table in target */
ALTER TABLE `invest`
    CHANGE `account` `account` varchar(256)  COLLATE utf8_general_ci NOT NULL COMMENT 'Solo para aportes de cash' after `project` ,
    ADD COLUMN `amount_original` int(6)   NULL COMMENT 'Importe introducido por el usuario' after `amount` ,
    ADD COLUMN `currency` varchar(4)  COLLATE utf8_general_ci NOT NULL DEFAULT 'EUR' COMMENT 'Divisa al aportar' after `amount_original` ,
    ADD COLUMN `currency_rate` decimal(9,5)   NOT NULL DEFAULT 1.00000 COMMENT 'Ratio de conversión a eurio al aportar' after `currency` ,
    CHANGE `status` `status` int(1)   NOT NULL COMMENT '-1 en proceso, 0 pendiente, 1 cobrado, 2 devuelto, 3 pagado al proyecto' after `currency_rate` ,
    CHANGE `anonymous` `anonymous` tinyint(1)   NULL after `status` ,
    CHANGE `resign` `resign` tinyint(1)   NULL after `anonymous` ,
    CHANGE `invested` `invested` date   NULL after `resign` ,
    CHANGE `charged` `charged` date   NULL after `invested` ,
    CHANGE `returned` `returned` date   NULL after `charged` ,
    CHANGE `preapproval` `preapproval` varchar(256)  COLLATE utf8_general_ci NULL COMMENT 'PreapprovalKey' after `returned` ,
    CHANGE `payment` `payment` varchar(256)  COLLATE utf8_general_ci NULL COMMENT 'PayKey' after `preapproval` ,
    CHANGE `transaction` `transaction` varchar(256)  COLLATE utf8_general_ci NULL COMMENT 'PaypalId' after `payment` ,
    CHANGE `method` `method` varchar(20)  COLLATE utf8_general_ci NOT NULL COMMENT 'Metodo de pago' after `transaction` ,
    CHANGE `admin` `admin` varchar(50)  COLLATE utf8_general_ci NULL COMMENT 'Admin que creó el aporte manual' after `method` ,
    CHANGE `campaign` `campaign` int(1) unsigned   NULL COMMENT 'si es un aporte de capital riego' after `admin` ,
    CHANGE `datetime` `datetime` timestamp   NULL DEFAULT CURRENT_TIMESTAMP after `campaign` ,
    ADD COLUMN `drops` bigint(20) unsigned   NULL COMMENT 'id del aporte que provoca este riego' after `datetime` ,
    ADD COLUMN `droped` bigint(20) unsigned   NULL COMMENT 'id del riego generado por este aporte' after `drops` ,
    ADD COLUMN `call` varchar(50)  COLLATE utf8_general_ci NULL COMMENT 'campaña dedonde sale el dinero' after `droped` ,
    CHANGE `issue` `issue` int(1)   NULL COMMENT 'Problemas con el cobro del aporte' after `call` ,
    ADD COLUMN `pool` int(1)   NULL COMMENT 'A reservar si el proyecto falla' after `issue` ,
    ADD KEY `convocatoria`(`call`) ,
    ADD KEY `proyecto`(`project`) ,
    ADD KEY `usuario`(`user`) ;

/* Alter table in target */
ALTER TABLE `invest_address`
    ADD COLUMN `namedest` tinytext  COLLATE utf8_general_ci NULL after `nif` ,
    ADD COLUMN `emaildest` tinytext  COLLATE utf8_general_ci NULL after `namedest` ,
    ADD COLUMN `regalo` int(1)   NULL DEFAULT 0 after `emaildest` ,
    ADD COLUMN `message` text  COLLATE utf8_general_ci NULL after `regalo` ;

/* Create table in target */
CREATE TABLE `invest_node`(
    `user_id` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `user_node` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `project_id` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `project_node` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `invest_id` bigint(20) NOT NULL  ,
    `invest_node` varchar(50) COLLATE utf8_general_ci NOT NULL  COMMENT 'Nodo en el que se hace el aporte' ,
    UNIQUE KEY `invest`(`invest_id`) ,
    KEY `invest_id`(`invest_id`) ,
    KEY `invest_node`(`invest_node`) ,
    KEY `project_id`(`project_id`) ,
    KEY `project_node`(`project_node`) ,
    KEY `user_id`(`user_id`) ,
    KEY `user_node`(`user_node`) ,
    CONSTRAINT `invest_node_ibfk_1`
    FOREIGN KEY (`user_node`) REFERENCES `node` (`id`) ON UPDATE CASCADE ,
    CONSTRAINT `invest_node_ibfk_2`
    FOREIGN KEY (`project_node`) REFERENCES `node` (`id`) ON UPDATE CASCADE ,
    CONSTRAINT `invest_node_ibfk_3`
    FOREIGN KEY (`invest_node`) REFERENCES `node` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Aportes por usuario/nodo a proyecto/nodo';


/* Alter table in target */
ALTER TABLE `invest_reward`
    ADD KEY `reward`(`reward`) ;

/* Alter table in target */
ALTER TABLE `license_lang`
    ADD COLUMN `pending` int(1)   NULL DEFAULT 0 COMMENT 'Debe revisarse la traducción' after `url` ;

/* Create table in target */
CREATE TABLE `log`(
    `id` bigint(20) unsigned NOT NULL  auto_increment ,
    `scope` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `target_type` varchar(10) COLLATE utf8_general_ci NULL  COMMENT 'tipo de objetivo' ,
    `target_id` varchar(50) COLLATE utf8_general_ci NULL  COMMENT 'registro objetivo' ,
    `text` text COLLATE utf8_general_ci NULL  ,
    `url` tinytext COLLATE utf8_general_ci NULL  ,
    `datetime` timestamp NOT NULL  DEFAULT CURRENT_TIMESTAMP ,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Log de cosas';


/* Alter table in target */
ALTER TABLE `mail`
    CHANGE `email` `email` char(255)  COLLATE utf8_general_ci NOT NULL after `id` ,
    ADD COLUMN `subject` char(255)  COLLATE utf8_general_ci NULL after `email` ,
    ADD COLUMN `content` longtext  COLLATE utf8_general_ci NOT NULL after `subject` ,
    CHANGE `template` `template` bigint(20) unsigned   NULL after `content` ,
    ADD COLUMN `node` varchar(50)  COLLATE utf8_general_ci NULL after `template` ,
    CHANGE `date` `date` timestamp   NOT NULL DEFAULT CURRENT_TIMESTAMP after `node` ,
    ADD COLUMN `lang` varchar(2)  COLLATE utf8_general_ci NULL COMMENT 'Idioma en el que se solicitó la plantilla' after `date` ,
    ADD COLUMN `sent` tinyint(4)   NULL after `lang` ,
    ADD COLUMN `error` tinytext  COLLATE utf8_general_ci NULL after `sent` ,
    DROP COLUMN `html` ,
    ADD KEY `email`(`email`) ,
    DROP KEY `id`, ADD UNIQUE KEY `id`(`id`,`email`) ,
    ADD KEY `node`(`node`) ,
    ADD KEY `template`(`template`) ;
ALTER TABLE `mail`
    ADD CONSTRAINT `mail_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ,
    ADD CONSTRAINT `mail_ibfk_2`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ,
    ADD CONSTRAINT `mail_ibfk_3`
    FOREIGN KEY (`template`) REFERENCES `template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;


/* Create table in target */
CREATE TABLE `mail_stats`(
    `id` bigint(20) unsigned NOT NULL  auto_increment ,
    `mail_id` bigint(20) unsigned NOT NULL  ,
    `email` char(150) COLLATE utf8_general_ci NOT NULL  ,
    `metric_id` bigint(20) unsigned NOT NULL  ,
    `counter` int(10) unsigned NOT NULL  DEFAULT 0 ,
    `created_at` datetime NOT NULL  ,
    `modified_at` timestamp NOT NULL  DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP ,
    PRIMARY KEY (`id`,`mail_id`,`email`,`metric_id`) ,
    KEY `email`(`email`) ,
    KEY `metric`(`metric_id`) ,
    KEY `mail_id`(`mail_id`) ,
    CONSTRAINT `mail_stats_ibfk_1`
    FOREIGN KEY (`metric_id`) REFERENCES `metric` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Create table in target */
CREATE TABLE `mail_stats_location`(
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
    CONSTRAINT `mail_stats_location_ibfk_1`
    FOREIGN KEY (`id`) REFERENCES `mail_stats` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Alter table in target */
ALTER TABLE `mailer_content`
    CHANGE `mail` `mail` bigint(20) unsigned   NOT NULL after `active` ,
    CHANGE `datetime` `datetime` timestamp   NOT NULL DEFAULT CURRENT_TIMESTAMP after `mail` ,
    CHANGE `blocked` `blocked` int(1)   NULL after `datetime` ,
    CHANGE `reply` `reply` varchar(255)  COLLATE utf8_general_ci NULL COMMENT 'Email remitente' after `blocked` ,
    CHANGE `reply_name` `reply_name` text  COLLATE utf8_general_ci NULL COMMENT 'Nombre remitente' after `reply` ,
    DROP COLUMN `subject` ,
    ADD KEY `mail`(`mail`) ;
ALTER TABLE `mailer_content`
    ADD CONSTRAINT `mailer_content_ibfk_1`
    FOREIGN KEY (`mail`) REFERENCES `mail` (`id`) ;


/* Alter table in target */
ALTER TABLE `mailer_limit` COMMENT='Para limitar el número de envios diarios' ;

/* Alter table in target */
ALTER TABLE `mailer_send`
    ADD COLUMN `sent` int(1)   NULL after `datetime` ,
    CHANGE `error` `error` text  COLLATE utf8_general_ci NULL after `sent` ,
    DROP COLUMN `sended` ;
ALTER TABLE `mailer_send`
    ADD CONSTRAINT `mailer_send_ibfk_1`
    FOREIGN KEY (`mailing`) REFERENCES `mailer_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;


/* Alter table in target */
ALTER TABLE `message_lang`
    ADD COLUMN `pending` int(1)   NULL DEFAULT 0 COMMENT 'Debe revisarse la traducción' after `message` ;

/* Create table in target */
CREATE TABLE `metric`(
    `id` bigint(20) unsigned NOT NULL  auto_increment ,
    `metric` char(255) COLLATE utf8_general_ci NOT NULL  ,
    `desc` char(255) COLLATE utf8_general_ci NULL  ,
    PRIMARY KEY (`id`) ,
    UNIQUE KEY `metric`(`metric`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Alter table in target */
ALTER TABLE `news`
    ADD COLUMN `image` varchar(255)  COLLATE utf8_general_ci NULL COMMENT 'Contiene nombre de archivo' after `order` ,
    ADD COLUMN `press_banner` tinyint(1)   NULL DEFAULT 0 COMMENT 'Para aparecer en banner prensa' after `image` ,
    ADD COLUMN `media_name` tinytext  COLLATE utf8_general_ci NULL COMMENT 'Medio de prensa en que se publica' after `press_banner` ;

/* Alter table in target */
ALTER TABLE `news_lang`
    ADD COLUMN `pending` int(1)   NULL DEFAULT 0 COMMENT 'Debe revisarse la traducción' after `url` ;

/* Alter table in target */
ALTER TABLE `node`
    ADD COLUMN `email` varchar(255)  COLLATE utf8_general_ci NOT NULL after `name` ,
    CHANGE `active` `active` tinyint(1)   NOT NULL after `email` ,
    ADD COLUMN `url` varchar(255)  COLLATE utf8_general_ci NOT NULL after `active` ,
    ADD COLUMN `subtitle` text  COLLATE utf8_general_ci NULL after `url` ,
    ADD COLUMN `logo` varchar(255)  COLLATE utf8_general_ci NULL COMMENT 'Contiene nombre de archivo' after `subtitle` ,
    ADD COLUMN `location` varchar(100)  COLLATE utf8_general_ci NULL after `logo` ,
    ADD COLUMN `description` text  COLLATE utf8_general_ci NULL after `location` ,
    ADD COLUMN `twitter` tinytext  COLLATE utf8_general_ci NULL after `description` ,
    ADD COLUMN `facebook` tinytext  COLLATE utf8_general_ci NULL after `twitter` ,
    ADD COLUMN `google` tinytext  COLLATE utf8_general_ci NULL after `facebook` ,
    ADD COLUMN `linkedin` tinytext  COLLATE utf8_general_ci NULL after `google` ,
    ADD COLUMN `label` varchar(255)  COLLATE utf8_general_ci NULL COMMENT 'Sello en proyectos' after `linkedin` ,
    ADD COLUMN `owner_background` varchar(255)  COLLATE utf8_general_ci NULL COMMENT 'Color de background módulo owner' after `label` ,
    ADD COLUMN `default_consultant` varchar(50)  COLLATE utf8_general_ci NULL COMMENT 'Asesor por defecto para el proyecto' after `owner_background` ,
    ADD COLUMN `sponsors_limit` int(2)   NULL COMMENT 'Número de sponsors permitidos para el canal' after `default_consultant` ,
    ADD KEY `default_consultant`(`default_consultant`) ;
ALTER TABLE `node`
    ADD CONSTRAINT `node_ibfk_1`
    FOREIGN KEY (`default_consultant`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE ;


/* Create table in target */
CREATE TABLE `node_data`(
    `node` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `projects` smallint(5) unsigned NULL  DEFAULT 0 ,
    `active` tinyint(3) unsigned NULL  DEFAULT 0 ,
    `success` smallint(5) unsigned NULL  DEFAULT 0 ,
    `investors` smallint(5) unsigned NULL  DEFAULT 0 ,
    `supporters` smallint(5) unsigned NULL  DEFAULT 0 ,
    `amount` mediumint(8) unsigned NULL  DEFAULT 0 ,
    `budget` mediumint(8) unsigned NULL  DEFAULT 0 ,
    `rest` mediumint(8) unsigned NULL  DEFAULT 0 ,
    `calls` tinyint(3) unsigned NULL  DEFAULT 0 ,
    `campaigns` tinyint(3) unsigned NULL  DEFAULT 0 ,
    `updated` timestamp NOT NULL  DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP ,
    PRIMARY KEY (`node`) ,
    CONSTRAINT `node_data_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Datos resumen nodo';


/* Create table in target */
CREATE TABLE `node_lang`(
    `id` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `lang` varchar(2) COLLATE utf8_general_ci NOT NULL  ,
    `subtitle` text COLLATE utf8_general_ci NULL  ,
    `description` text COLLATE utf8_general_ci NULL  ,
    `pending` int(1) NULL  DEFAULT 0 COMMENT 'Debe revisarse la traducción' ,
    UNIQUE KEY `id_lang`(`id`,`lang`) ,
    CONSTRAINT `node_lang_ibfk_1`
    FOREIGN KEY (`id`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


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
ALTER TABLE `page_node`
    ADD COLUMN `pending` int(1)   NULL DEFAULT 0 COMMENT 'Debe revisarse la traducción' after `content` ,
    ADD KEY `node`(`node`) ;
ALTER TABLE `page_node`
    ADD CONSTRAINT `page_node_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE ;


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
    CONSTRAINT `patron_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE
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
ALTER TABLE `post`
    CHANGE `image` `image` varchar(255)  COLLATE utf8_general_ci NULL COMMENT 'Contiene nombre de archivo' after `media` ,
    ADD COLUMN `num_comments` int(10) unsigned   NULL COMMENT 'Número de comentarios que recibe el post' after `author` ,
    ADD KEY `pie`(`footer`) ,
    ADD KEY `portada`(`home`) ,
    ADD KEY `publicadas`(`publish`) ;

/* Alter table in target */
ALTER TABLE `post_image`
    CHANGE `post` `post` bigint(20) unsigned   NOT NULL first ,
    CHANGE `image` `image` varchar(255)  COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Contiene nombre de archivo' after `post` ;
ALTER TABLE `post_image`
    ADD CONSTRAINT `post_image_ibfk_1`
    FOREIGN KEY (`post`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;


/* Alter table in target */
ALTER TABLE `post_lang`
    ADD COLUMN `blog` int(20)   NOT NULL after `id` ,
    CHANGE `lang` `lang` varchar(2)  COLLATE utf8_general_ci NOT NULL after `blog` ,
    CHANGE `title` `title` tinytext  COLLATE utf8_general_ci NULL after `lang` ,
    CHANGE `text` `text` longtext  COLLATE utf8_general_ci NULL after `title` ,
    CHANGE `legend` `legend` text  COLLATE utf8_general_ci NULL after `text` ,
    CHANGE `media` `media` tinytext  COLLATE utf8_general_ci NULL after `legend` ,
    ADD COLUMN `pending` int(1)   NULL DEFAULT 0 COMMENT 'Debe revisarse la traducción' after `media` ,
    ADD KEY `blog`(`blog`) ;

/* Create table in target */
CREATE TABLE `post_node`(
    `post` bigint(20) unsigned NOT NULL  ,
    `node` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `order` int(11) NULL  DEFAULT 1 ,
    PRIMARY KEY (`post`,`node`) ,
    KEY `node`(`node`) ,
    CONSTRAINT `post_node_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Entradas para la portada de nodos';


/* Alter table in target */
ALTER TABLE `project`
    ADD COLUMN `currency` varchar(4)  COLLATE utf8_general_ci NOT NULL DEFAULT 'EUR' COMMENT 'Divisa del proyecto' after `lang` ,
    ADD COLUMN `currency_rate` decimal(9,5)   NOT NULL DEFAULT 1.00000 COMMENT 'Ratio al crear el proyecto' after `currency` ,
    CHANGE `status` `status` int(1)   NOT NULL after `currency_rate` ,
    CHANGE `translate` `translate` int(1)   NOT NULL DEFAULT 0 after `status` ,
    CHANGE `progress` `progress` int(3)   NOT NULL after `translate` ,
    CHANGE `owner` `owner` varchar(50)  COLLATE utf8_general_ci NOT NULL COMMENT 'usuario que lo ha creado' after `progress` ,
    CHANGE `node` `node` varchar(50)  COLLATE utf8_general_ci NOT NULL COMMENT 'nodo en el que se ha creado' after `owner` ,
    CHANGE `amount` `amount` int(6)   NULL COMMENT 'acumulado actualmente' after `node` ,
    ADD COLUMN `mincost` int(5)   NULL COMMENT 'minimo coste' after `amount` ,
    ADD COLUMN `maxcost` int(5)   NULL COMMENT 'optimo' after `mincost` ,
    CHANGE `days` `days` int(3)   NOT NULL DEFAULT 0 COMMENT 'Dias restantes' after `maxcost` ,
    ADD COLUMN `num_investors` int(10) unsigned   NULL COMMENT 'Numero inversores' after `days` ,
    ADD COLUMN `popularity` int(10) unsigned   NULL COMMENT 'Popularidad del proyecto' after `num_investors` ,
    ADD COLUMN `num_messengers` int(10) unsigned   NULL COMMENT 'Número de personas que envían mensajes' after `popularity` ,
    ADD COLUMN `num_posts` int(10) unsigned   NULL COMMENT 'Número de post' after `num_messengers` ,
    CHANGE `created` `created` date   NULL after `num_posts` ,
    CHANGE `updated` `updated` date   NULL after `created` ,
    CHANGE `published` `published` date   NULL after `updated` ,
    CHANGE `success` `success` date   NULL after `published` ,
    CHANGE `closed` `closed` date   NULL after `success` ,
    CHANGE `passed` `passed` date   NULL after `closed` ,
    CHANGE `contract_name` `contract_name` varchar(255)  COLLATE utf8_general_ci NULL after `passed` ,
    CHANGE `contract_nif` `contract_nif` varchar(15)  COLLATE utf8_general_ci NULL COMMENT 'Guardar sin espacios ni puntos ni guiones' after `contract_name` ,
    CHANGE `phone` `phone` varchar(20)  COLLATE utf8_general_ci NULL COMMENT 'guardar talcual' after `contract_nif` ,
    CHANGE `contract_email` `contract_email` varchar(255)  COLLATE utf8_general_ci NULL after `phone` ,
    CHANGE `address` `address` tinytext  COLLATE utf8_general_ci NULL after `contract_email` ,
    CHANGE `zipcode` `zipcode` varchar(10)  COLLATE utf8_general_ci NULL after `address` ,
    CHANGE `location` `location` varchar(255)  COLLATE utf8_general_ci NULL after `zipcode` ,
    CHANGE `country` `country` varchar(50)  COLLATE utf8_general_ci NULL after `location` ,
    CHANGE `image` `image` varchar(255)  COLLATE utf8_general_ci NULL COMMENT 'Contiene nombre de archivo' after `country` ,
    CHANGE `description` `description` text  COLLATE utf8_general_ci NULL after `image` ,
    CHANGE `motivation` `motivation` text  COLLATE utf8_general_ci NULL after `description` ,
    CHANGE `video` `video` varchar(256)  COLLATE utf8_general_ci NULL after `motivation` ,
    CHANGE `video_usubs` `video_usubs` int(1)   NOT NULL DEFAULT 0 after `video` ,
    CHANGE `about` `about` text  COLLATE utf8_general_ci NULL after `video_usubs` ,
    CHANGE `goal` `goal` text  COLLATE utf8_general_ci NULL after `about` ,
    CHANGE `related` `related` text  COLLATE utf8_general_ci NULL after `goal` ,
    ADD COLUMN `spread` text  COLLATE utf8_general_ci NULL after `related` ,
    CHANGE `reward` `reward` text  COLLATE utf8_general_ci NULL after `spread` ,
    CHANGE `category` `category` varchar(50)  COLLATE utf8_general_ci NULL after `reward` ,
    CHANGE `keywords` `keywords` tinytext  COLLATE utf8_general_ci NULL COMMENT 'Separadas por comas' after `category` ,
    CHANGE `media` `media` varchar(256)  COLLATE utf8_general_ci NULL after `keywords` ,
    CHANGE `media_usubs` `media_usubs` int(1)   NOT NULL DEFAULT 0 after `media` ,
    CHANGE `currently` `currently` int(1)   NULL after `media_usubs` ,
    CHANGE `project_location` `project_location` varchar(256)  COLLATE utf8_general_ci NULL after `currently` ,
    CHANGE `scope` `scope` int(1)   NULL COMMENT 'Ambito de alcance' after `project_location` ,
    CHANGE `resource` `resource` text  COLLATE utf8_general_ci NULL after `scope` ,
    CHANGE `comment` `comment` text  COLLATE utf8_general_ci NULL COMMENT 'Comentario para los admin' after `resource` ,
    CHANGE `contract_entity` `contract_entity` int(1)   NOT NULL DEFAULT 0 after `comment` ,
    CHANGE `contract_birthdate` `contract_birthdate` date   NULL after `contract_entity` ,
    CHANGE `entity_office` `entity_office` varchar(255)  COLLATE utf8_general_ci NULL COMMENT 'Cargo del responsable' after `contract_birthdate` ,
    CHANGE `entity_name` `entity_name` varchar(255)  COLLATE utf8_general_ci NULL after `entity_office` ,
    CHANGE `entity_cif` `entity_cif` varchar(10)  COLLATE utf8_general_ci NULL COMMENT 'Guardar sin espacios ni puntos ni guiones' after `entity_name` ,
    CHANGE `post_address` `post_address` tinytext  COLLATE utf8_general_ci NULL after `entity_cif` ,
    CHANGE `secondary_address` `secondary_address` int(11)   NOT NULL DEFAULT 0 after `post_address` ,
    CHANGE `post_zipcode` `post_zipcode` varchar(10)  COLLATE utf8_general_ci NULL after `secondary_address` ,
    CHANGE `post_location` `post_location` varchar(255)  COLLATE utf8_general_ci NULL after `post_zipcode` ,
    CHANGE `post_country` `post_country` varchar(50)  COLLATE utf8_general_ci NULL after `post_location` ,
    ADD COLUMN `amount_users` int(10) unsigned   NULL COMMENT 'Recaudación proveniente de los usuarios' after `post_country` ,
    ADD COLUMN `amount_call` int(10) unsigned   NULL COMMENT 'Recaudación proveniente de la convocatoria' after `amount_users` ,
    ADD COLUMN `maxproj` int(5)   NULL COMMENT 'Dinero que puede conseguir un proyecto de la convocatoria' after `amount_call` ,
    ADD KEY `estado`(`status`) ,
    ADD KEY `nodo`(`node`) ;
ALTER TABLE `project`
    ADD CONSTRAINT `project_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE ,
    ADD CONSTRAINT `project_ibfk_2`
    FOREIGN KEY (`owner`) REFERENCES `user` (`id`) ON UPDATE CASCADE ;


/* Alter table in target */
ALTER TABLE `project_account`
    ADD COLUMN `fee` int(1)   NOT NULL DEFAULT 4 COMMENT 'porcentaje de comisión goteo' after `allowpp` ;

/* Alter table in target */
ALTER TABLE `project_category`
    CHANGE `category` `category` bigint(20) unsigned   NOT NULL after `project` ,
    ADD KEY `category`(`category`) ,
    ADD KEY `project`(`project`) ;

/* Create table in target */
CREATE TABLE `project_conf`(
    `project` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `noinvest` int(1) NOT NULL  DEFAULT 0 COMMENT 'No se permiten más aportes' ,
    `watch` tinyint(1) NOT NULL  DEFAULT 0 COMMENT 'Vigilar el proyecto' ,
    `days_round1` int(4) NULL  DEFAULT 40 COMMENT 'Días que dura la primera ronda desde la publicación del proyecto' ,
    `days_round2` int(4) NULL  DEFAULT 40 COMMENT 'Días que dura la segunda ronda desde la publicación del proyecto' ,
    `one_round` tinyint(1) NOT NULL  DEFAULT 0 COMMENT 'Si el proyecto tiene una unica ronda' ,
    `help_license` tinyint(1) NOT NULL  DEFAULT 0 COMMENT 'Si necesita ayuda en licencias' ,
    `help_cost` tinyint(1) NOT NULL  DEFAULT 0 COMMENT 'Si necesita ayuda en costes' ,
    PRIMARY KEY (`project`) ,
    CONSTRAINT `project_conf_ibfk_1`
    FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Configuraciones para proyectos';


/* Create table in target */
CREATE TABLE `project_data`(
    `project` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `updated` timestamp NOT NULL  DEFAULT CURRENT_TIMESTAMP ,
    `invested` int(6) unsigned NOT NULL  DEFAULT 0 COMMENT 'Mostrado en termometro al cerrar' ,
    `fee` int(6) unsigned NOT NULL  DEFAULT 0 COMMENT 'comisiones cobradas por bancos y paypal a goteo' ,
    `issue` int(6) unsigned NOT NULL  DEFAULT 0 COMMENT 'importe de las incidencias' ,
    `amount` int(6) unsigned NOT NULL  DEFAULT 0 COMMENT 'recaudaro realmente' ,
    `goteo` int(6) unsigned NOT NULL  DEFAULT 0 COMMENT 'comision goteo' ,
    `percent` int(1) unsigned NOT NULL  DEFAULT 8 COMMENT 'porcentaje comision goteo' ,
    `comment` text COLLATE utf8_general_ci NULL  COMMENT 'comentarios y/o listado de incidencias' ,
    PRIMARY KEY (`project`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='datos de informe financiero';


/* Alter table in target */
ALTER TABLE `project_image`
    CHANGE `image` `image` varchar(255)  COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Contiene nombre de archivo' after `project` ,
    ADD KEY `proyecto-seccion`(`project`,`section`) ;
ALTER TABLE `project_image`
    ADD CONSTRAINT `project_image_ibfk_1`
    FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;


/* Alter table in target */
ALTER TABLE `project_lang`
    ADD COLUMN `pending` int(1)   NULL DEFAULT 0 COMMENT 'Debe revisarse la traducción' after `subtitle` ;

/* Create table in target */
CREATE TABLE `project_location`(
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
    KEY `locable`(`locable`) ,
    CONSTRAINT `project_location_ibfk_1`
    FOREIGN KEY (`id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Create table in target */
CREATE TABLE `project_open_tag`(
    `project` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `open_tag` int(12) NOT NULL  ,
    UNIQUE KEY `project_open_tag`(`project`,`open_tag`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Agrupacion de los proyectos';


/* Alter table in target */
ALTER TABLE `promote`
    ADD KEY `activos`(`active`) ,
    ADD KEY `project`(`project`) ;
ALTER TABLE `promote`
    ADD CONSTRAINT `promote_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ,
    ADD CONSTRAINT `promote_ibfk_2`
    FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;


/* Alter table in target */
ALTER TABLE `promote_lang`
    ADD COLUMN `pending` int(1)   NULL DEFAULT 0 COMMENT 'Debe revisarse la traducción' after `description` ;
ALTER TABLE `promote_lang`
    ADD CONSTRAINT `promote_lang_ibfk_1`
    FOREIGN KEY (`id`) REFERENCES `promote` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;


/* Alter table in target */
ALTER TABLE `reward`
    ADD COLUMN `order` tinyint(4)   NOT NULL DEFAULT 1 COMMENT 'Orden para retornos colectivos' after `url` ,
    ADD COLUMN `bonus` tinyint(1)   NOT NULL DEFAULT 0 COMMENT 'Retorno colectivo adicional' after `order` ,
    ADD KEY `icon`(`icon`) ,
    ADD KEY `project`(`project`) ,
    ADD KEY `type`(`type`) ;

/* Alter table in target */
ALTER TABLE `reward_lang`
    ADD COLUMN `project` varchar(50)  COLLATE utf8_general_ci NOT NULL after `id` ,
    CHANGE `lang` `lang` varchar(2)  COLLATE utf8_general_ci NOT NULL after `project` ,
    CHANGE `reward` `reward` tinytext  COLLATE utf8_general_ci NULL after `lang` ,
    CHANGE `description` `description` text  COLLATE utf8_general_ci NULL after `reward` ,
    CHANGE `other` `other` tinytext  COLLATE utf8_general_ci NULL after `description` ,
    ADD COLUMN `pending` int(1)   NULL DEFAULT 0 COMMENT 'Debe revisarse la traducción' after `other` ,
    ADD KEY `project`(`project`) ;

/* Alter table in target */
ALTER TABLE `sponsor`
    CHANGE `image` `image` varchar(255)  COLLATE utf8_general_ci NULL COMMENT 'Contiene nombre de archivo' after `url` ,
    ADD COLUMN `node` varchar(50)  COLLATE utf8_general_ci NOT NULL after `order` ,
    DROP KEY `id` ,
    ADD KEY `node`(`node`) ;
ALTER TABLE `sponsor`
    ADD CONSTRAINT `sponsor_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;


/* Create table in target */
CREATE TABLE `stories`(
    `id` bigint(20) unsigned NOT NULL  auto_increment ,
    `node` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `project` varchar(50) COLLATE utf8_general_ci NULL  ,
    `order` smallint(5) unsigned NOT NULL  DEFAULT 1 ,
    `image` varchar(255) COLLATE utf8_general_ci NULL  COMMENT 'Contiene nombre de archivo' ,
    `active` int(1) NOT NULL  DEFAULT 0 ,
    `title` tinytext COLLATE utf8_general_ci NULL  ,
    `description` text COLLATE utf8_general_ci NULL  ,
    `review` text COLLATE utf8_general_ci NULL  ,
    `url` tinytext COLLATE utf8_general_ci NULL  ,
    `post` bigint(20) unsigned NULL  ,
    PRIMARY KEY (`id`) ,
    KEY `node`(`node`) ,
    CONSTRAINT `stories_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Historias existosas';


/* Create table in target */
CREATE TABLE `stories_lang`(
    `id` bigint(20) unsigned NOT NULL  ,
    `lang` varchar(2) COLLATE utf8_general_ci NOT NULL  ,
    `title` tinytext COLLATE utf8_general_ci NULL  ,
    `description` text COLLATE utf8_general_ci NULL  ,
    `review` text COLLATE utf8_general_ci NULL  ,
    `pending` int(1) NULL  DEFAULT 0 COMMENT 'Debe revisarse la traducción' ,
    UNIQUE KEY `id_lang`(`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Alter table in target */
ALTER TABLE `support`
    ADD KEY `hilo`(`thread`) ,
    ADD KEY `proyecto`(`project`) ;

/* Alter table in target */
ALTER TABLE `support_lang`
    ADD COLUMN `project` varchar(50)  COLLATE utf8_general_ci NOT NULL after `id` ,
    CHANGE `lang` `lang` varchar(2)  COLLATE utf8_general_ci NOT NULL after `project` ,
    CHANGE `support` `support` tinytext  COLLATE utf8_general_ci NULL after `lang` ,
    CHANGE `description` `description` text  COLLATE utf8_general_ci NULL after `support` ,
    ADD COLUMN `pending` int(1)   NULL DEFAULT 0 COMMENT 'Debe revisarse la traducción' after `description` ,
    ADD KEY `project`(`project`) ;

/* Alter table in target */
ALTER TABLE `tag_lang`
    ADD COLUMN `pending` int(1)   NULL DEFAULT 0 COMMENT 'Debe revisarse la traducción' after `name` ;

/* Alter table in target */
ALTER TABLE `task`
    ADD KEY `node`(`node`) , ENGINE=InnoDB;
ALTER TABLE `task`
    ADD CONSTRAINT `task_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;


/* Alter table in target */
ALTER TABLE `template_lang`
    ADD COLUMN `pending` int(1)   NULL DEFAULT 0 COMMENT 'Debe revisarse la traducción' after `text` ,
    DROP KEY `id_lang` ,
    ADD PRIMARY KEY(`id`,`lang`) ;
ALTER TABLE `template_lang`
    ADD CONSTRAINT `template_lang_ibfk_1`
    FOREIGN KEY (`id`) REFERENCES `template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;


/* Alter table in target */
ALTER TABLE `text`
    ADD COLUMN `pending` int(1)   NULL DEFAULT 0 COMMENT 'Debe revisarse la traducción' after `text` ;

/* Alter table in target */
ALTER TABLE `user`
    CHANGE `password` `password` varchar(40)  COLLATE utf8_general_ci NOT NULL after `email` ,
    CHANGE `avatar` `avatar` varchar(255)  COLLATE utf8_general_ci NULL COMMENT 'Contiene nombre de archivo' after `active` ,
    ADD COLUMN `amount` int(7)   NULL COMMENT 'Cantidad total aportada' after `linkedin` ,
    ADD COLUMN `num_patron` int(10) unsigned   NULL COMMENT 'Num. proyectos patronizados' after `amount` ,
    ADD COLUMN `num_patron_active` int(10) unsigned   NULL COMMENT 'Num. proyectos patronizados activos' after `num_patron` ,
    CHANGE `worth` `worth` int(7)   NULL after `num_patron_active` ,
    CHANGE `created` `created` timestamp   NULL after `worth` ,
    CHANGE `modified` `modified` timestamp   NULL DEFAULT CURRENT_TIMESTAMP  on update CURRENT_TIMESTAMP after `created` ,
    CHANGE `token` `token` tinytext  COLLATE utf8_general_ci NOT NULL after `modified` ,
    CHANGE `hide` `hide` tinyint(1)   NOT NULL DEFAULT 0 COMMENT 'No se ve publicamente' after `token` ,
    CHANGE `confirmed` `confirmed` int(1)   NOT NULL DEFAULT 0 after `hide` ,
    CHANGE `lang` `lang` varchar(2)  COLLATE utf8_general_ci NULL DEFAULT 'es' after `confirmed` ,
    CHANGE `node` `node` varchar(50)  COLLATE utf8_general_ci NULL after `lang` ,
    ADD COLUMN `num_invested` int(10) unsigned   NULL COMMENT 'Num. proyectos cofinanciados' after `node` ,
    ADD COLUMN `num_owned` int(10) unsigned   NULL COMMENT 'Num. proyectos publicados' after `num_invested` ,
    ADD KEY `coordenadas`(`location`) ,
    ADD KEY `nodo`(`node`) ;
ALTER TABLE `user`
    ADD CONSTRAINT `user_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE ;


/* Create table in target */
CREATE TABLE `user_api`(
    `user_id` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `key` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `expiration_date` datetime NULL  ,
    PRIMARY KEY (`user_id`) ,
    CONSTRAINT `user_api_ibfk_1`
    FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Create table in target */
CREATE TABLE `user_call`(
    `user` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `call` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    PRIMARY KEY (`user`,`call`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Asignacion de convocatorias a admines';


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


/* Alter table in target */
ALTER TABLE `user_interest`
    ADD KEY `interes`(`interest`) ,
    ADD KEY `usuario`(`user`) ;

/* Create table in target */
CREATE TABLE `user_location`(
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
    KEY `locable`(`locable`) ,
    CONSTRAINT `user_location_ibfk_1`
    FOREIGN KEY (`id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Alter table in target */
ALTER TABLE `user_login`
    ADD CONSTRAINT `user_login_ibfk_1`
    FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;


/* Create table in target */
CREATE TABLE `user_node`(
    `user` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `node` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    PRIMARY KEY (`user`,`node`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Create table in target */
CREATE TABLE `user_pool`(
    `user` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `amount` int(7) unsigned NOT NULL  DEFAULT 0 ,
    PRIMARY KEY (`user`) ,
    CONSTRAINT `user_pool_ibfk_1`
    FOREIGN KEY (`user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Alter table in target */
ALTER TABLE `user_prefer`
    ADD COLUMN `comlang` varchar(2)  COLLATE utf8_general_ci NULL after `tips` ,
    ADD COLUMN `currency` varchar(3)  COLLATE utf8_general_ci NULL after `comlang` ;

/* Create table in target */
CREATE TABLE `user_project`(
    `user` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `project` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    UNIQUE KEY `user`(`user`,`project`) ,
    KEY `project`(`project`) ,
    CONSTRAINT `user_project_ibfk_1`
    FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ,
    CONSTRAINT `user_project_ibfk_2`
    FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Alter table in target */
ALTER TABLE `user_role`
    CHANGE `node_id` `node_id` varchar(50)  COLLATE utf8_general_ci NULL after `role_id` ,
    DROP KEY `PRIMARY` ;
ALTER TABLE `user_role`
    ADD CONSTRAINT `user_role_ibfk_1`
    FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ,
    ADD CONSTRAINT `user_role_ibfk_2`
    FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ,
    ADD CONSTRAINT `user_role_ibfk_3`
    FOREIGN KEY (`node_id`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;


/* Create table in target */
CREATE TABLE `user_vip`(
    `user` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `image` varchar(255) COLLATE utf8_general_ci NULL  COMMENT 'Contiene nombre de archivo' ,
    PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Datos usuario colaborador';


/* Alter table in target */
ALTER TABLE `worthcracy_lang`
    ADD COLUMN `pending` int(1)   NULL DEFAULT 0 COMMENT 'Debe revisarse la traducción' after `name` ;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
