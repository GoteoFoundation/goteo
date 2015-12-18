/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

/* Foreign Keys must be dropped in the target to ensure that requires changes can be done*/

ALTER TABLE `invest_node`
    DROP FOREIGN KEY `invest_node_ibfk_1`  ,
    DROP FOREIGN KEY `invest_node_ibfk_2`  ,
    DROP FOREIGN KEY `invest_node_ibfk_3`  ,
    DROP FOREIGN KEY `invest_node_ibfk_4`  ,
    DROP FOREIGN KEY `invest_node_ibfk_5`  ,
    DROP FOREIGN KEY `invest_node_ibfk_6`  ;


/* Create table in target */
CREATE TABLE `banner`(
    `id` bigint(20) unsigned NOT NULL  auto_increment ,
    `node` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `project` varchar(50) COLLATE utf8_general_ci NULL  ,
    `order` smallint(5) unsigned NOT NULL  DEFAULT 1 ,
    `image` varchar(255) COLLATE utf8_general_ci NULL  COMMENT 'Contiene nombre de archivo' ,
    `active` int(1) NOT NULL  DEFAULT 0 ,
    `title` tinytext COLLATE utf8_general_ci NULL  ,
    `description` text COLLATE utf8_general_ci NULL  ,
    `url` tinytext COLLATE utf8_general_ci NULL  ,
    PRIMARY KEY (`id`) ,
    KEY `banner_ibfk_1`(`node`) ,
    KEY `banner_ibfk_2`(`project`) ,
    CONSTRAINT `banner_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE ,
    CONSTRAINT `banner_ibfk_2`
    FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Proyectos en banner superior';


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
CREATE TABLE `faq`(
    `id` bigint(20) unsigned NOT NULL  auto_increment ,
    `node` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `section` varchar(50) COLLATE utf8_general_ci NOT NULL  DEFAULT 'node' ,
    `title` tinytext COLLATE utf8_general_ci NULL  ,
    `description` text COLLATE utf8_general_ci NULL  ,
    `order` tinyint(4) NOT NULL  DEFAULT 1 ,
    PRIMARY KEY (`id`) ,
    UNIQUE KEY `id`(`id`) ,
    KEY `node`(`node`) ,
    CONSTRAINT `faq_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Preguntas frecuentes';


/* Create table in target */
CREATE TABLE `home`(
    `item` varchar(10) COLLATE utf8_general_ci NOT NULL  ,
    `type` varchar(5) COLLATE utf8_general_ci NOT NULL  DEFAULT 'main' COMMENT 'lateral o central' ,
    `node` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `order` smallint(5) unsigned NOT NULL  DEFAULT 1 ,
    UNIQUE KEY `item_node`(`item`,`node`) ,
    KEY `node`(`node`) ,
    CONSTRAINT `home_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Elementos en portada';


/* Create table in target */
CREATE TABLE `info`(
    `id` bigint(20) unsigned NOT NULL  auto_increment ,
    `node` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `title` tinytext COLLATE utf8_general_ci NULL  ,
    `text` longtext COLLATE utf8_general_ci NULL  COMMENT 'texto de la entrada' ,
    `media` tinytext COLLATE utf8_general_ci NULL  ,
    `publish` tinyint(1) NOT NULL  DEFAULT 0 ,
    `order` int(11) NULL  DEFAULT 1 ,
    `legend` text COLLATE utf8_general_ci NULL  ,
    `gallery` varchar(2000) COLLATE utf8_general_ci NULL  COMMENT 'Galería de imagenes' ,
    `image` varchar(255) COLLATE utf8_general_ci NULL  COMMENT 'Imagen principal' ,
    `share_facebook` tinytext COLLATE utf8_general_ci NULL  ,
    `share_twitter` tinytext COLLATE utf8_general_ci NULL  ,
    PRIMARY KEY (`id`) ,
    UNIQUE KEY `id`(`id`) ,
    KEY `node`(`node`) ,
    CONSTRAINT `info_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Entradas about';


/* Create table in target */
CREATE TABLE `info_image`(
    `info` bigint(20) unsigned NOT NULL  ,
    `image` varchar(255) COLLATE utf8_general_ci NOT NULL  DEFAULT '' COMMENT 'Contiene nombre de archivo' ,
    PRIMARY KEY (`info`,`image`) ,
    CONSTRAINT `info_image_ibfk_1`
    FOREIGN KEY (`info`) REFERENCES `info` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Create table in target */
CREATE TABLE `invest`(
    `id` bigint(20) unsigned NOT NULL  auto_increment ,
    `user` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `project` varchar(50) COLLATE utf8_general_ci NULL  ,
    `account` varchar(256) COLLATE utf8_general_ci NOT NULL  COMMENT 'Solo para aportes de cash' ,
    `amount` int(6) NOT NULL  ,
    `amount_original` int(6) NULL  COMMENT 'Importe introducido por el usuario' ,
    `currency` varchar(4) COLLATE utf8_general_ci NOT NULL  DEFAULT 'EUR' COMMENT 'Divisa al aportar' ,
    `currency_rate` decimal(9,5) NOT NULL  DEFAULT 1.00000 COMMENT 'Ratio de conversión a eurio al aportar' ,
    `status` int(1) NOT NULL  COMMENT '-1 en proceso, 0 pendiente, 1 cobrado, 2 devuelto, 3 pagado al proyecto' ,
    `anonymous` tinyint(1) NULL  ,
    `resign` tinyint(1) NULL  ,
    `invested` date NULL  ,
    `charged` date NULL  ,
    `returned` date NULL  ,
    `preapproval` varchar(256) COLLATE utf8_general_ci NULL  COMMENT 'PreapprovalKey' ,
    `payment` varchar(256) COLLATE utf8_general_ci NULL  COMMENT 'PayKey' ,
    `transaction` varchar(256) COLLATE utf8_general_ci NULL  COMMENT 'PaypalId' ,
    `method` varchar(20) COLLATE utf8_general_ci NOT NULL  COMMENT 'Metodo de pago' ,
    `admin` varchar(50) COLLATE utf8_general_ci NULL  COMMENT 'Admin que creó el aporte manual' ,
    `campaign` int(1) unsigned NULL  COMMENT 'si es un aporte de capital riego' ,
    `datetime` timestamp NULL  DEFAULT CURRENT_TIMESTAMP ,
    `drops` bigint(20) unsigned NULL  COMMENT 'id del aporte que provoca este riego' ,
    `droped` bigint(20) unsigned NULL  COMMENT 'id del riego generado por este aporte' ,
    `call` varchar(50) COLLATE utf8_general_ci NULL  COMMENT 'campaña dedonde sale el dinero' ,
    `issue` int(1) NULL  COMMENT 'Problemas con el cobro del aporte' ,
    `pool` int(1) NULL  COMMENT 'A reservar si el proyecto falla' ,
    PRIMARY KEY (`id`) ,
    UNIQUE KEY `id`(`id`) ,
    KEY `usuario`(`user`) ,
    KEY `proyecto`(`project`) ,
    KEY `convocatoria`(`call`) ,
    CONSTRAINT `invest_ibfk_1`
    FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON UPDATE CASCADE ,
    CONSTRAINT `invest_ibfk_2`
    FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Aportes monetarios a proyectos';


/* Create table in target */
CREATE TABLE `invest_address`(
    `invest` bigint(20) unsigned NOT NULL  ,
    `user` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `address` tinytext COLLATE utf8_general_ci NULL  ,
    `zipcode` varchar(10) COLLATE utf8_general_ci NULL  ,
    `location` varchar(255) COLLATE utf8_general_ci NULL  ,
    `country` varchar(50) COLLATE utf8_general_ci NULL  ,
    `name` varchar(255) COLLATE utf8_general_ci NULL  ,
    `nif` varchar(10) COLLATE utf8_general_ci NULL  ,
    `namedest` tinytext COLLATE utf8_general_ci NULL  ,
    `emaildest` tinytext COLLATE utf8_general_ci NULL  ,
    `regalo` int(1) NULL  DEFAULT 0 ,
    `message` text COLLATE utf8_general_ci NULL  ,
    PRIMARY KEY (`invest`) ,
    KEY `user`(`user`) ,
    CONSTRAINT `invest_address_ibfk_1`
    FOREIGN KEY (`invest`) REFERENCES `invest` (`id`) ON UPDATE CASCADE ,
    CONSTRAINT `invest_address_ibfk_2`
    FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Dirección de entrega de recompensa';


/* Create table in target */
CREATE TABLE `invest_detail`(
    `invest` bigint(20) unsigned NOT NULL  ,
    `type` varchar(30) COLLATE utf8_general_ci NOT NULL  ,
    `log` text COLLATE utf8_general_ci NOT NULL  ,
    `date` timestamp NOT NULL  DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP ,
    UNIQUE KEY `invest_type`(`invest`,`type`) ,
    KEY `invest`(`invest`) ,
    CONSTRAINT `invest_detail_ibfk_1`
    FOREIGN KEY (`invest`) REFERENCES `invest` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Detalles de los aportes';


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
CREATE TABLE `invest_reward`(
    `invest` bigint(20) unsigned NOT NULL  ,
    `reward` bigint(20) unsigned NOT NULL  ,
    `fulfilled` tinyint(1) NOT NULL  DEFAULT 0 ,
    UNIQUE KEY `invest`(`invest`,`reward`) ,
    KEY `reward`(`reward`) ,
    CONSTRAINT `invest_reward_ibfk_1`
    FOREIGN KEY (`invest`) REFERENCES `invest` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ,
    CONSTRAINT `invest_reward_ibfk_2`
    FOREIGN KEY (`reward`) REFERENCES `reward` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Recompensas elegidas al aportar';


/* Create table in target */
CREATE TABLE `mail`(
    `id` bigint(20) unsigned NOT NULL  auto_increment ,
    `email` char(255) COLLATE utf8_general_ci NOT NULL  ,
    `subject` char(255) COLLATE utf8_general_ci NULL  ,
    `content` longtext COLLATE utf8_general_ci NOT NULL  ,
    `template` bigint(20) unsigned NULL  ,
    `node` varchar(50) COLLATE utf8_general_ci NULL  ,
    `date` timestamp NOT NULL  DEFAULT CURRENT_TIMESTAMP ,
    `lang` varchar(2) COLLATE utf8_general_ci NULL  COMMENT 'Idioma en el que se solicitó la plantilla' ,
    `sent` tinyint(4) NULL  ,
    `error` tinytext COLLATE utf8_general_ci NULL  ,
    PRIMARY KEY (`id`) ,
    UNIQUE KEY `id`(`id`,`email`) ,
    KEY `email`(`email`) ,
    KEY `node`(`node`) ,
    KEY `template`(`template`) ,
    CONSTRAINT `mail_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ,
    CONSTRAINT `mail_ibfk_2`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ,
    CONSTRAINT `mail_ibfk_3`
    FOREIGN KEY (`template`) REFERENCES `template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Contenido enviado por email para el -si no ves-';


/* Create table in target */
CREATE TABLE `mailer_content`(
    `id` int(20) unsigned NOT NULL  auto_increment ,
    `active` int(1) NOT NULL  DEFAULT 1 ,
    `mail` bigint(20) unsigned NOT NULL  ,
    `datetime` timestamp NOT NULL  DEFAULT CURRENT_TIMESTAMP ,
    `blocked` int(1) NULL  ,
    `reply` varchar(255) COLLATE utf8_general_ci NULL  COMMENT 'Email remitente' ,
    `reply_name` text COLLATE utf8_general_ci NULL  COMMENT 'Nombre remitente' ,
    PRIMARY KEY (`id`) ,
    KEY `mail`(`mail`) ,
    CONSTRAINT `mailer_content_ibfk_1`
    FOREIGN KEY (`mail`) REFERENCES `mail` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Contenido a enviar';


/* Create table in target */
CREATE TABLE `mailer_send`(
    `id` bigint(20) unsigned NOT NULL  auto_increment ,
    `mailing` int(20) unsigned NOT NULL  COMMENT 'Id de mailer_content' ,
    `user` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `email` varchar(256) COLLATE utf8_general_ci NOT NULL  ,
    `name` varchar(100) COLLATE utf8_general_ci NOT NULL  ,
    `datetime` timestamp NOT NULL  DEFAULT CURRENT_TIMESTAMP ,
    `sent` int(1) NULL  ,
    `error` text COLLATE utf8_general_ci NULL  ,
    `blocked` int(1) NULL  ,
    UNIQUE KEY `id`(`id`) ,
    KEY `mailing`(`mailing`) ,
    CONSTRAINT `mailer_send_ibfk_1`
    FOREIGN KEY (`mailing`) REFERENCES `mailer_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Destinatarios pendientes y realizados';


/* Create table in target */
CREATE TABLE `node`(
    `id` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `name` varchar(256) COLLATE utf8_general_ci NOT NULL  ,
    `email` varchar(255) COLLATE utf8_general_ci NOT NULL  ,
    `active` tinyint(1) NOT NULL  ,
    `url` varchar(255) COLLATE utf8_general_ci NOT NULL  ,
    `subtitle` text COLLATE utf8_general_ci NULL  ,
    `logo` varchar(255) COLLATE utf8_general_ci NULL  COMMENT 'Contiene nombre de archivo' ,
    `location` varchar(100) COLLATE utf8_general_ci NULL  ,
    `description` text COLLATE utf8_general_ci NULL  ,
    `twitter` tinytext COLLATE utf8_general_ci NULL  ,
    `facebook` tinytext COLLATE utf8_general_ci NULL  ,
    `google` tinytext COLLATE utf8_general_ci NULL  ,
    `linkedin` tinytext COLLATE utf8_general_ci NULL  ,
    `label` varchar(255) COLLATE utf8_general_ci NULL  COMMENT 'Sello en proyectos' ,
    `owner_background` varchar(255) COLLATE utf8_general_ci NULL  COMMENT 'Color de background módulo owner' ,
    `default_consultant` varchar(50) COLLATE utf8_general_ci NULL  COMMENT 'Asesor por defecto para el proyecto' ,
    `sponsors_limit` int(2) NULL  COMMENT 'Número de sponsors permitidos para el canal' ,
    PRIMARY KEY (`id`) ,
    KEY `default_consultant`(`default_consultant`) ,
    CONSTRAINT `node_ibfk_1`
    FOREIGN KEY (`default_consultant`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Nodos';


/* Create table in target */
CREATE TABLE `page_node`(
    `page` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `node` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `lang` varchar(2) COLLATE utf8_general_ci NOT NULL  ,
    `name` tinytext COLLATE utf8_general_ci NULL  ,
    `description` text COLLATE utf8_general_ci NULL  ,
    `content` longtext COLLATE utf8_general_ci NULL  ,
    `pending` int(1) NULL  DEFAULT 0 COMMENT 'Debe revisarse la traducción' ,
    UNIQUE KEY `page`(`page`,`node`,`lang`) ,
    KEY `node`(`node`) ,
    CONSTRAINT `page_node_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Contenidos de las paginas';


/* Create table in target */
CREATE TABLE `project`(
    `id` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `name` tinytext COLLATE utf8_general_ci NULL  ,
    `subtitle` tinytext COLLATE utf8_general_ci NULL  ,
    `lang` varchar(2) COLLATE utf8_general_ci NULL  DEFAULT 'es' ,
    `currency` varchar(4) COLLATE utf8_general_ci NOT NULL  DEFAULT 'EUR' COMMENT 'Divisa del proyecto' ,
    `currency_rate` decimal(9,5) NOT NULL  DEFAULT 1.00000 COMMENT 'Ratio al crear el proyecto' ,
    `status` int(1) NOT NULL  ,
    `translate` int(1) NOT NULL  DEFAULT 0 ,
    `progress` int(3) NOT NULL  ,
    `owner` varchar(50) COLLATE utf8_general_ci NOT NULL  COMMENT 'usuario que lo ha creado' ,
    `node` varchar(50) COLLATE utf8_general_ci NOT NULL  COMMENT 'nodo en el que se ha creado' ,
    `amount` int(6) NULL  COMMENT 'acumulado actualmente' ,
    `mincost` int(5) NULL  COMMENT 'minimo coste' ,
    `maxcost` int(5) NULL  COMMENT 'optimo' ,
    `days` int(3) NOT NULL  DEFAULT 0 COMMENT 'Dias restantes' ,
    `num_investors` int(10) unsigned NULL  COMMENT 'Numero inversores' ,
    `popularity` int(10) unsigned NULL  COMMENT 'Popularidad del proyecto' ,
    `num_messengers` int(10) unsigned NULL  COMMENT 'Número de personas que envían mensajes' ,
    `num_posts` int(10) unsigned NULL  COMMENT 'Número de post' ,
    `created` date NULL  ,
    `updated` date NULL  ,
    `published` date NULL  ,
    `success` date NULL  ,
    `closed` date NULL  ,
    `passed` date NULL  ,
    `contract_name` varchar(255) COLLATE utf8_general_ci NULL  ,
    `contract_nif` varchar(15) COLLATE utf8_general_ci NULL  COMMENT 'Guardar sin espacios ni puntos ni guiones' ,
    `phone` varchar(20) COLLATE utf8_general_ci NULL  COMMENT 'guardar talcual' ,
    `contract_email` varchar(255) COLLATE utf8_general_ci NULL  ,
    `address` tinytext COLLATE utf8_general_ci NULL  ,
    `zipcode` varchar(10) COLLATE utf8_general_ci NULL  ,
    `location` varchar(255) COLLATE utf8_general_ci NULL  ,
    `country` varchar(50) COLLATE utf8_general_ci NULL  ,
    `image` varchar(255) COLLATE utf8_general_ci NULL  COMMENT 'Contiene nombre de archivo' ,
    `description` text COLLATE utf8_general_ci NULL  ,
    `motivation` text COLLATE utf8_general_ci NULL  ,
    `video` varchar(256) COLLATE utf8_general_ci NULL  ,
    `video_usubs` int(1) NOT NULL  DEFAULT 0 ,
    `about` text COLLATE utf8_general_ci NULL  ,
    `goal` text COLLATE utf8_general_ci NULL  ,
    `related` text COLLATE utf8_general_ci NULL  ,
    `spread` text COLLATE utf8_general_ci NULL  ,
    `reward` text COLLATE utf8_general_ci NULL  ,
    `category` varchar(50) COLLATE utf8_general_ci NULL  ,
    `keywords` tinytext COLLATE utf8_general_ci NULL  COMMENT 'Separadas por comas' ,
    `media` varchar(256) COLLATE utf8_general_ci NULL  ,
    `media_usubs` int(1) NOT NULL  DEFAULT 0 ,
    `currently` int(1) NULL  ,
    `project_location` varchar(256) COLLATE utf8_general_ci NULL  ,
    `scope` int(1) NULL  COMMENT 'Ambito de alcance' ,
    `resource` text COLLATE utf8_general_ci NULL  ,
    `comment` text COLLATE utf8_general_ci NULL  COMMENT 'Comentario para los admin' ,
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
    `amount_users` int(10) unsigned NULL  COMMENT 'Recaudación proveniente de los usuarios' ,
    `amount_call` int(10) unsigned NULL  COMMENT 'Recaudación proveniente de la convocatoria' ,
    `maxproj` int(5) NULL  COMMENT 'Dinero que puede conseguir un proyecto de la convocatoria' ,
    PRIMARY KEY (`id`) ,
    KEY `owner`(`owner`) ,
    KEY `nodo`(`node`) ,
    KEY `estado`(`status`) ,
    CONSTRAINT `project_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE ,
    CONSTRAINT `project_ibfk_2`
    FOREIGN KEY (`owner`) REFERENCES `user` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Proyectos de la plataforma';


/* Create table in target */
CREATE TABLE `project_account`(
    `project` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `bank` tinytext COLLATE utf8_general_ci NULL  ,
    `bank_owner` tinytext COLLATE utf8_general_ci NULL  ,
    `paypal` tinytext COLLATE utf8_general_ci NULL  ,
    `paypal_owner` tinytext COLLATE utf8_general_ci NULL  ,
    `allowpp` int(1) NULL  ,
    `fee` int(1) NOT NULL  DEFAULT 4 COMMENT 'porcentaje de comisión goteo' ,
    PRIMARY KEY (`project`) ,
    CONSTRAINT `project_account_ibfk_1`
    FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Cuentas bancarias de proyecto';


/* Create table in target */
CREATE TABLE `project_image`(
    `project` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `image` varchar(255) COLLATE utf8_general_ci NOT NULL  DEFAULT '' COMMENT 'Contiene nombre de archivo' ,
    `section` varchar(50) COLLATE utf8_general_ci NULL  ,
    `url` tinytext COLLATE utf8_general_ci NULL  ,
    `order` tinyint(4) NULL  ,
    PRIMARY KEY (`project`,`image`) ,
    KEY `proyecto-seccion`(`project`,`section`) ,
    CONSTRAINT `project_image_ibfk_1`
    FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Create table in target */
CREATE TABLE `promote`(
    `id` bigint(20) unsigned NOT NULL  auto_increment ,
    `node` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `project` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `title` tinytext COLLATE utf8_general_ci NULL  ,
    `description` text COLLATE utf8_general_ci NULL  ,
    `order` smallint(5) unsigned NOT NULL  DEFAULT 1 ,
    `active` int(1) NOT NULL  DEFAULT 0 ,
    PRIMARY KEY (`id`) ,
    UNIQUE KEY `project_node`(`node`,`project`) ,
    UNIQUE KEY `id`(`id`) ,
    KEY `activos`(`active`) ,
    KEY `project`(`project`) ,
    CONSTRAINT `promote_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ,
    CONSTRAINT `promote_ibfk_2`
    FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Proyectos destacados';


/* Create table in target */
CREATE TABLE `promote_lang`(
    `id` bigint(20) unsigned NOT NULL  ,
    `lang` varchar(2) COLLATE utf8_general_ci NOT NULL  ,
    `title` tinytext COLLATE utf8_general_ci NULL  ,
    `description` text COLLATE utf8_general_ci NULL  ,
    `pending` int(1) NULL  DEFAULT 0 COMMENT 'Debe revisarse la traducción' ,
    UNIQUE KEY `id_lang`(`id`,`lang`) ,
    CONSTRAINT `promote_lang_ibfk_1`
    FOREIGN KEY (`id`) REFERENCES `promote` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Create table in target */
CREATE TABLE `purpose`(
    `text` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `purpose` text COLLATE utf8_general_ci NOT NULL  ,
    `html` tinyint(1) NULL  COMMENT 'Si el texto lleva formato html' ,
    `group` varchar(50) COLLATE utf8_general_ci NOT NULL  DEFAULT 'general' COMMENT 'Agrupacion de uso' ,
    PRIMARY KEY (`text`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Explicación del propósito de los textos';


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


/* Create table in target */
CREATE TABLE `sponsor`(
    `id` bigint(20) unsigned NOT NULL  auto_increment ,
    `name` tinytext COLLATE utf8_general_ci NOT NULL  ,
    `url` tinytext COLLATE utf8_general_ci NULL  ,
    `image` varchar(255) COLLATE utf8_general_ci NULL  COMMENT 'Contiene nombre de archivo' ,
    `order` int(11) NOT NULL  DEFAULT 1 ,
    `node` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    PRIMARY KEY (`id`) ,
    KEY `node`(`node`) ,
    CONSTRAINT `sponsor_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Patrocinadores';


/* Alter table in target */
ALTER TABLE `stories`
    ADD COLUMN `pool_image` varchar(255)  COLLATE utf8_general_ci NULL after `post` ,
    ADD COLUMN `pool` int(1)   NOT NULL DEFAULT 0 after `pool_image` ,
    ADD COLUMN `text_position` varchar(50)  COLLATE utf8_general_ci NULL after `pool` ;
ALTER TABLE `stories`
    ADD CONSTRAINT `stories_ibfk_2`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE ;


/* Create table in target */
CREATE TABLE `task`(
    `id` bigint(20) unsigned NOT NULL  auto_increment ,
    `node` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `text` text COLLATE utf8_general_ci NOT NULL  ,
    `url` tinytext COLLATE utf8_general_ci NULL  ,
    `done` varchar(50) COLLATE utf8_general_ci NULL  ,
    `datetime` timestamp NOT NULL  DEFAULT CURRENT_TIMESTAMP ,
    PRIMARY KEY (`id`) ,
    UNIQUE KEY `id`(`id`) ,
    KEY `node`(`node`) ,
    CONSTRAINT `task_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Tareas pendientes de admin';


/* Create table in target */
CREATE TABLE `user`(
    `id` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `name` varchar(100) COLLATE utf8_general_ci NOT NULL  ,
    `location` varchar(255) COLLATE utf8_general_ci NULL  ,
    `email` varchar(255) COLLATE utf8_general_ci NOT NULL  ,
    `password` varchar(40) COLLATE utf8_general_ci NOT NULL  ,
    `about` text COLLATE utf8_general_ci NULL  ,
    `keywords` tinytext COLLATE utf8_general_ci NULL  ,
    `active` tinyint(1) NOT NULL  ,
    `avatar` varchar(255) COLLATE utf8_general_ci NULL  COMMENT 'Contiene nombre de archivo' ,
    `contribution` text COLLATE utf8_general_ci NULL  ,
    `twitter` tinytext COLLATE utf8_general_ci NULL  ,
    `facebook` tinytext COLLATE utf8_general_ci NULL  ,
    `google` tinytext COLLATE utf8_general_ci NULL  ,
    `identica` tinytext COLLATE utf8_general_ci NULL  ,
    `linkedin` tinytext COLLATE utf8_general_ci NULL  ,
    `amount` int(7) NULL  COMMENT 'Cantidad total aportada' ,
    `num_patron` int(10) unsigned NULL  COMMENT 'Num. proyectos patronizados' ,
    `num_patron_active` int(10) unsigned NULL  COMMENT 'Num. proyectos patronizados activos' ,
    `worth` int(7) NULL  ,
    `created` timestamp NULL  ,
    `modified` timestamp NULL  DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP ,
    `token` tinytext COLLATE utf8_general_ci NOT NULL  ,
    `hide` tinyint(1) NOT NULL  DEFAULT 0 COMMENT 'No se ve publicamente' ,
    `confirmed` int(1) NOT NULL  DEFAULT 0 ,
    `lang` varchar(2) COLLATE utf8_general_ci NULL  DEFAULT 'es' ,
    `node` varchar(50) COLLATE utf8_general_ci NULL  ,
    `num_invested` int(10) unsigned NULL  COMMENT 'Num. proyectos cofinanciados' ,
    `num_owned` int(10) unsigned NULL  COMMENT 'Num. proyectos publicados' ,
    PRIMARY KEY (`id`) ,
    KEY `nodo`(`node`) ,
    KEY `coordenadas`(`location`) ,
    CONSTRAINT `user_ibfk_1`
    FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Drop in Second database */
DROP TABLE `user_donation`;


/* Create table in target */
CREATE TABLE `user_login`(
    `user` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `provider` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `oauth_token` text COLLATE utf8_general_ci NOT NULL  ,
    `oauth_token_secret` text COLLATE utf8_general_ci NOT NULL  ,
    `datetime` timestamp NULL  DEFAULT CURRENT_TIMESTAMP ,
    PRIMARY KEY (`user`,`oauth_token`(255)) ,
    CONSTRAINT `user_login_ibfk_1`
    FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Create table in target */
CREATE TABLE `user_role`(
    `user_id` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `role_id` varchar(50) COLLATE utf8_general_ci NOT NULL  ,
    `node_id` varchar(50) COLLATE utf8_general_ci NULL  ,
    `datetime` timestamp NULL  DEFAULT CURRENT_TIMESTAMP ,
    KEY `user_FK`(`user_id`) ,
    KEY `role_FK`(`role_id`) ,
    KEY `node_FK`(`node_id`) ,
    CONSTRAINT `user_role_ibfk_1`
    FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ,
    CONSTRAINT `user_role_ibfk_2`
    FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ,
    CONSTRAINT `user_role_ibfk_3`
    FOREIGN KEY (`node_id`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


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
