SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS=0;

/*!40101 SET NAMES utf8 */;

/*Table structure for table `banner` */

CREATE TABLE IF NOT EXISTS `banner` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `node` varchar(50) NOT NULL,
  `project` varchar(50) DEFAULT NULL,
  `order` smallint(5) unsigned NOT NULL DEFAULT '1',
  `image` varchar(255) DEFAULT NULL COMMENT 'Contiene nombre de archivo',
  `active` int(1) NOT NULL DEFAULT '0',
  `title` tinytext,
  `description` text,
  `url` tinytext,
  PRIMARY KEY (`id`),
  KEY `banner_ibfk_1` (`node`),
  KEY `banner_ibfk_2` (`project`),
  CONSTRAINT `banner_ibfk_1` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `banner_ibfk_2` FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=296 DEFAULT CHARSET=utf8 COMMENT='Proyectos en banner superior';

/*Table structure for table `banner_lang` */

CREATE TABLE IF NOT EXISTS `banner_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `description` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `blog` */

CREATE TABLE IF NOT EXISTS `blog` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(10) NOT NULL,
  `owner` varchar(50) NOT NULL COMMENT 'la id del proyecto o nodo',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=618 DEFAULT CHARSET=utf8 COMMENT='Blogs de nodo o proyecto';

/*Table structure for table `category` */

CREATE TABLE IF NOT EXISTS `category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext,
  `description` text,
  `order` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='Categorias de los proyectos';

/*Table structure for table `category_lang` */

CREATE TABLE IF NOT EXISTS `category_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` tinytext,
  `description` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  PRIMARY KEY (`id`,`lang`),
  KEY `lang` (`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `comment` */

CREATE TABLE IF NOT EXISTS `comment` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post` bigint(20) unsigned NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `text` text NOT NULL,
  `user` varchar(50) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=820 DEFAULT CHARSET=utf8 COMMENT='Comentarios';

/*Table structure for table `cost` */

CREATE TABLE IF NOT EXISTS `cost` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project` varchar(50) NOT NULL,
  `cost` tinytext,
  `description` text,
  `type` varchar(50) DEFAULT NULL,
  `amount` int(5) DEFAULT '0',
  `required` tinyint(1) DEFAULT '0',
  `from` date DEFAULT NULL,
  `until` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20413 DEFAULT CHARSET=utf8 COMMENT='Desglose de costes de proyectos';

/*Table structure for table `cost_lang` */

CREATE TABLE IF NOT EXISTS `cost_lang` (
  `id` int(20) NOT NULL,
  `project` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `cost` tinytext,
  `description` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`),
  KEY `project` (`project`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `criteria` */

CREATE TABLE IF NOT EXISTS `criteria` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `section` varchar(50) NOT NULL DEFAULT 'node',
  `title` tinytext,
  `description` text,
  `order` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COMMENT='Criterios de puntuación';

/*Table structure for table `criteria_lang` */

CREATE TABLE IF NOT EXISTS `criteria_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `description` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `document` */

CREATE TABLE IF NOT EXISTS `document` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contract` varchar(50) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `size` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contract` (`contract`),
  CONSTRAINT `document_ibfk_1` FOREIGN KEY (`contract`) REFERENCES `contract` (`project`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1009 DEFAULT CHARSET=utf8;

/*Table structure for table `faq` */

CREATE TABLE IF NOT EXISTS `faq` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `node` varchar(50) NOT NULL,
  `section` varchar(50) NOT NULL DEFAULT 'node',
  `title` tinytext,
  `description` text,
  `order` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `node` (`node`),
  CONSTRAINT `faq_ibfk_1` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=utf8 COMMENT='Preguntas frecuentes';

/*Table structure for table `faq_lang` */

CREATE TABLE IF NOT EXISTS `faq_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `description` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `feed` */

CREATE TABLE IF NOT EXISTS `feed` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` tinytext NOT NULL,
  `url` tinytext,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `scope` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `html` text NOT NULL,
  `image` varchar(255) DEFAULT NULL COMMENT 'Contiene nombre de archivo',
  `target_type` varchar(10) DEFAULT NULL COMMENT 'tipo de objetivo',
  `target_id` varchar(50) DEFAULT NULL COMMENT 'registro objetivo',
  `post` int(20) unsigned DEFAULT NULL COMMENT 'Entrada de blog',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `scope` (`scope`),
  KEY `type` (`type`),
  KEY `target_type` (`target_type`)
) ENGINE=InnoDB AUTO_INCREMENT=401994 DEFAULT CHARSET=utf8 COMMENT='Log de eventos';

/*Table structure for table `glossary` */

CREATE TABLE IF NOT EXISTS `glossary` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` tinytext,
  `text` longtext COMMENT 'texto de la entrada',
  `media` tinytext,
  `legend` text,
  `image` varchar(255) DEFAULT NULL COMMENT 'Imagen principal',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='Entradas para el glosario';

/*Table structure for table `glossary_image` */

CREATE TABLE IF NOT EXISTS `glossary_image` (
  `glossary` bigint(20) unsigned NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT 'Contiene nombre de archivo',
  PRIMARY KEY (`glossary`,`image`),
  CONSTRAINT `glossary_image_ibfk_1` FOREIGN KEY (`glossary`) REFERENCES `glossary` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `glossary_lang` */

CREATE TABLE IF NOT EXISTS `glossary_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `text` longtext,
  `legend` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `home` */

CREATE TABLE IF NOT EXISTS `home` (
  `item` varchar(10) NOT NULL,
  `type` varchar(5) NOT NULL DEFAULT 'main' COMMENT 'lateral o central',
  `node` varchar(50) NOT NULL,
  `order` smallint(5) unsigned NOT NULL DEFAULT '1',
  UNIQUE KEY `item_node` (`item`,`node`),
  KEY `node` (`node`),
  CONSTRAINT `home_ibfk_1` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Elementos en portada';

/*Table structure for table `icon` */

CREATE TABLE IF NOT EXISTS `icon` (
  `id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` tinytext,
  `group` varchar(50) DEFAULT NULL COMMENT 'exclusivo para grupo',
  `order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Iconos para retorno/recompensa';

/*Table structure for table `icon_lang` */

CREATE TABLE IF NOT EXISTS `icon_lang` (
  `id` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` tinytext,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `icon_license` */

CREATE TABLE IF NOT EXISTS `icon_license` (
  `icon` varchar(50) NOT NULL,
  `license` varchar(50) NOT NULL,
  UNIQUE KEY `icon` (`icon`,`license`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Licencias para cada icono, solo social';

/*Table structure for table `image` */

CREATE TABLE IF NOT EXISTS `image` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `size` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36969 DEFAULT CHARSET=utf8;

/*Table structure for table `info` */

CREATE TABLE IF NOT EXISTS `info` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `node` varchar(50) NOT NULL,
  `title` tinytext,
  `text` longtext COMMENT 'texto de la entrada',
  `media` tinytext,
  `publish` tinyint(1) NOT NULL DEFAULT '0',
  `order` int(11) DEFAULT '1',
  `legend` text,
  `gallery` varchar(2000) DEFAULT NULL COMMENT 'Galería de imagenes',
  `image` varchar(255) DEFAULT NULL COMMENT 'Imagen principal',
  `share_facebook` tinytext,
  `share_twitter` tinytext,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `node` (`node`),
  CONSTRAINT `info_ibfk_1` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='Entradas about';

/*Table structure for table `info_image` */

CREATE TABLE IF NOT EXISTS `info_image` (
  `info` bigint(20) unsigned NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT 'Contiene nombre de archivo',
  PRIMARY KEY (`info`,`image`),
  CONSTRAINT `info_image_ibfk_1` FOREIGN KEY (`info`) REFERENCES `info` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `info_lang` */

CREATE TABLE IF NOT EXISTS `info_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `text` longtext,
  `legend` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  `share_facebook` tinytext,
  `share_twitter` tinytext,
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `invest` */

CREATE TABLE IF NOT EXISTS `invest` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(50) NOT NULL,
  `project` varchar(50) NOT NULL,
  `account` varchar(256) NOT NULL COMMENT 'Solo para aportes de cash',
  `amount` int(6) NOT NULL,
  `amount_original` int(6) DEFAULT NULL COMMENT 'Importe introducido por el usuario',
  `currency` varchar(4) NOT NULL DEFAULT 'EUR' COMMENT 'Divisa al aportar',
  `currency_rate` decimal(9,5) NOT NULL DEFAULT '1.00000' COMMENT 'Ratio de conversión a eurio al aportar',
  `status` int(1) NOT NULL COMMENT '-1 en proceso, 0 pendiente, 1 cobrado, 2 devuelto, 3 pagado al proyecto',
  `anonymous` tinyint(1) DEFAULT NULL,
  `resign` tinyint(1) DEFAULT NULL,
  `invested` date DEFAULT NULL,
  `charged` date DEFAULT NULL,
  `returned` date DEFAULT NULL,
  `preapproval` varchar(256) DEFAULT NULL COMMENT 'PreapprovalKey',
  `payment` varchar(256) DEFAULT NULL COMMENT 'PayKey',
  `transaction` varchar(256) DEFAULT NULL COMMENT 'PaypalId',
  `method` varchar(20) NOT NULL COMMENT 'Metodo de pago',
  `admin` varchar(50) DEFAULT NULL COMMENT 'Admin que creó el aporte manual',
  `campaign` int(1) unsigned DEFAULT NULL COMMENT 'si es un aporte de capital riego',
  `datetime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `drops` bigint(20) unsigned DEFAULT NULL COMMENT 'id del aporte que provoca este riego',
  `droped` bigint(20) unsigned DEFAULT NULL COMMENT 'id del riego generado por este aporte',
  `call` varchar(50) DEFAULT NULL COMMENT 'campaña dedonde sale el dinero',
  `issue` int(1) DEFAULT NULL COMMENT 'Problemas con el cobro del aporte',
  `pool` int(1) DEFAULT NULL COMMENT 'A reservar si el proyecto falla',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `usuario` (`user`),
  KEY `proyecto` (`project`),
  KEY `convocatoria` (`call`),
  CONSTRAINT `invest_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `invest_ibfk_2` FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=77737 DEFAULT CHARSET=utf8 COMMENT='Aportes monetarios a proyectos';

/*Table structure for table `invest_address` */

CREATE TABLE IF NOT EXISTS `invest_address` (
  `invest` bigint(20) unsigned NOT NULL,
  `user` varchar(50) NOT NULL,
  `address` tinytext,
  `zipcode` varchar(10) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `nif` varchar(10) DEFAULT NULL,
  `namedest` tinytext,
  `emaildest` tinytext,
  `regalo` int(1) DEFAULT '0',
  `message` text,
  PRIMARY KEY (`invest`),
  KEY `user` (`user`),
  CONSTRAINT `invest_address_ibfk_1` FOREIGN KEY (`invest`) REFERENCES `invest` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `invest_address_ibfk_2` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Dirección de entrega de recompensa';

/*Table structure for table `invest_detail` */

CREATE TABLE IF NOT EXISTS `invest_detail` (
  `invest` bigint(20) unsigned NOT NULL,
  `type` varchar(30) NOT NULL,
  `log` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `invest_type` (`invest`,`type`),
  KEY `invest` (`invest`),
  CONSTRAINT `invest_detail_ibfk_1` FOREIGN KEY (`invest`) REFERENCES `invest` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Detalles de los aportes';

/*Table structure for table `invest_node` */

CREATE TABLE IF NOT EXISTS `invest_node` (
  `user_id` varchar(50) NOT NULL,
  `user_node` varchar(50) NOT NULL,
  `project_id` varchar(50) NOT NULL,
  `project_node` varchar(50) NOT NULL,
  `invest_id` bigint(20) unsigned NOT NULL,
  `invest_node` varchar(50) NOT NULL COMMENT 'Nodo en el que se hace el aporte',
  UNIQUE KEY `invest` (`invest_id`),
  KEY `invest_id` (`invest_id`),
  KEY `invest_node` (`invest_node`),
  KEY `project_id` (`project_id`),
  KEY `project_node` (`project_node`),
  KEY `user_id` (`user_id`),
  KEY `user_node` (`user_node`),
  CONSTRAINT `invest_node_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `invest_node_ibfk_5` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `invest_node_ibfk_6` FOREIGN KEY (`invest_id`) REFERENCES `invest` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `invest_node_ibfk_1` FOREIGN KEY (`user_node`) REFERENCES `node` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `invest_node_ibfk_2` FOREIGN KEY (`project_node`) REFERENCES `node` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `invest_node_ibfk_3` FOREIGN KEY (`invest_node`) REFERENCES `node` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Aportes por usuario/nodo a proyecto/nodo';

/*Table structure for table `invest_reward` */

CREATE TABLE IF NOT EXISTS `invest_reward` (
  `invest` bigint(20) unsigned NOT NULL,
  `reward` bigint(20) unsigned NOT NULL,
  `fulfilled` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `invest` (`invest`,`reward`),
  KEY `reward` (`reward`),
  CONSTRAINT `invest_reward_ibfk_1` FOREIGN KEY (`invest`) REFERENCES `invest` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `invest_reward_ibfk_2` FOREIGN KEY (`reward`) REFERENCES `reward` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Recompensas elegidas al aportar';

/*Table structure for table `license` */

CREATE TABLE IF NOT EXISTS `license` (
  `id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` tinytext,
  `group` varchar(50) DEFAULT NULL COMMENT 'grupo de restriccion de menor a mayor',
  `url` varchar(256) DEFAULT NULL,
  `order` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Licencias de distribucion';

/*Table structure for table `license_lang` */

CREATE TABLE IF NOT EXISTS `license_lang` (
  `id` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` tinytext,
  `url` varchar(256) DEFAULT NULL,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `log` */

CREATE TABLE IF NOT EXISTS `log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `scope` varchar(50) NOT NULL,
  `target_type` varchar(10) DEFAULT NULL COMMENT 'tipo de objetivo',
  `target_id` varchar(50) DEFAULT NULL COMMENT 'registro objetivo',
  `text` text,
  `url` tinytext,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36219 DEFAULT CHARSET=utf8 COMMENT='Log de cosas';

/*Table structure for table `mail` */

CREATE TABLE IF NOT EXISTS `mail` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` char(255) NOT NULL,
  `subject` char(255) DEFAULT NULL,
  `content` longtext NOT NULL,
  `template` bigint(20) unsigned DEFAULT NULL,
  `node` varchar(50) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lang` varchar(2) DEFAULT NULL COMMENT 'Idioma en el que se solicitó la plantilla',
  `sent` tinyint(4) DEFAULT NULL,
  `error` tinytext,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`email`),
  KEY `email` (`email`),
  KEY `node` (`node`),
  KEY `template` (`template`),
  CONSTRAINT `mail_ibfk_1` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `mail_ibfk_2` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `mail_ibfk_3` FOREIGN KEY (`template`) REFERENCES `template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1258003 DEFAULT CHARSET=utf8 COMMENT='Contenido enviado por email para el -si no ves-';

/*Table structure for table `mail_stats` */

CREATE TABLE IF NOT EXISTS `mail_stats` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `mail_id` bigint(20) unsigned NOT NULL,
  `email` char(150) NOT NULL,
  `metric_id` bigint(20) unsigned NOT NULL,
  `counter` int(10) unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `modified_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`,`mail_id`,`email`,`metric_id`),
  KEY `email` (`email`),
  KEY `metric` (`metric_id`),
  KEY `mail_id` (`mail_id`),
  CONSTRAINT `mail_stats_ibfk_1` FOREIGN KEY (`metric_id`) REFERENCES `metric` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3567159 DEFAULT CHARSET=utf8;

/*Table structure for table `mail_stats_location` */

CREATE TABLE IF NOT EXISTS `mail_stats_location` (
  `id` bigint(20) unsigned NOT NULL,
  `latitude` decimal(16,14) NOT NULL,
  `longitude` decimal(16,14) NOT NULL,
  `method` varchar(50) NOT NULL DEFAULT 'ip',
  `locable` tinyint(1) NOT NULL DEFAULT '0',
  `city` varchar(255) NOT NULL,
  `region` varchar(255) NOT NULL,
  `country` varchar(150) NOT NULL,
  `country_code` varchar(2) NOT NULL,
  `info` varchar(255) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `locable` (`locable`),
  CONSTRAINT `mail_stats_location_ibfk_1` FOREIGN KEY (`id`) REFERENCES `mail_stats` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `mailer_content` */

CREATE TABLE IF NOT EXISTS `mailer_content` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `active` int(1) NOT NULL DEFAULT '1',
  `mail` bigint(20) unsigned NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `blocked` int(1) DEFAULT NULL,
  `reply` varchar(255) DEFAULT NULL COMMENT 'Email remitente',
  `reply_name` text COMMENT 'Nombre remitente',
  PRIMARY KEY (`id`),
  KEY `mail` (`mail`),
  CONSTRAINT `mailer_content_ibfk_1` FOREIGN KEY (`mail`) REFERENCES `mail` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10429 DEFAULT CHARSET=utf8 COMMENT='Contenido a enviar';

/*Table structure for table `mailer_control` */

CREATE TABLE IF NOT EXISTS `mailer_control` (
  `email` char(150) NOT NULL,
  `bounces` int(10) unsigned NOT NULL,
  `complaints` int(10) unsigned NOT NULL,
  `action` enum('allow','deny') DEFAULT 'allow',
  `last_reason` char(255) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Lista negra para bounces y complaints';

/*Table structure for table `mailer_limit` */

CREATE TABLE IF NOT EXISTS `mailer_limit` (
  `hora` time NOT NULL COMMENT 'Hora envio',
  `num` int(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Cuantos',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`hora`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Para limitar el número de envios diarios';

/*Table structure for table `mailer_send` */

CREATE TABLE IF NOT EXISTS `mailer_send` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `mailing` int(20) unsigned NOT NULL COMMENT 'Id de mailer_content',
  `user` varchar(50) NOT NULL,
  `email` varchar(256) NOT NULL,
  `name` varchar(100) NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sent` int(1) DEFAULT NULL,
  `error` text,
  `blocked` int(1) DEFAULT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `mailing` (`mailing`),
  CONSTRAINT `mailer_send_ibfk_1` FOREIGN KEY (`mailing`) REFERENCES `mailer_content` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4029201 DEFAULT CHARSET=utf8 COMMENT='Destinatarios pendientes y realizados';

/*Table structure for table `message` */

CREATE TABLE IF NOT EXISTS `message` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(50) NOT NULL,
  `project` varchar(50) NOT NULL,
  `thread` bigint(20) unsigned DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `message` text NOT NULL,
  `blocked` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'No se puede modificar ni borrar',
  `closed` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'No se puede responder',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12802 DEFAULT CHARSET=utf8 COMMENT='Mensajes de usuarios en proyecto';

/*Table structure for table `message_lang` */

CREATE TABLE IF NOT EXISTS `message_lang` (
  `id` int(20) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `message` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `metric` */

CREATE TABLE IF NOT EXISTS `metric` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `metric` char(255) NOT NULL,
  `desc` char(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `metric` (`metric`)
) ENGINE=InnoDB AUTO_INCREMENT=8508 DEFAULT CHARSET=utf8;

/*Table structure for table `news` */

CREATE TABLE IF NOT EXISTS `news` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` tinytext NOT NULL,
  `description` text COMMENT 'Entradilla',
  `url` tinytext NOT NULL,
  `order` int(11) NOT NULL DEFAULT '1',
  `image` varchar(255) DEFAULT NULL COMMENT 'Contiene nombre de archivo',
  `press_banner` tinyint(1) DEFAULT '0' COMMENT 'Para aparecer en banner prensa',
  `media_name` tinytext COMMENT 'Medio de prensa en que se publica',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8 COMMENT='Noticias en la cabecera';

/*Table structure for table `news_lang` */

CREATE TABLE IF NOT EXISTS `news_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `description` text,
  `url` tinytext,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `node` */

CREATE TABLE IF NOT EXISTS `node` (
  `id` varchar(50) NOT NULL,
  `name` varchar(256) NOT NULL,
  `email` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `url` varchar(255) NOT NULL,
  `subtitle` text,
  `logo` varchar(255) DEFAULT NULL COMMENT 'Contiene nombre de archivo',
  `location` varchar(100) DEFAULT NULL,
  `description` text,
  `twitter` tinytext,
  `facebook` tinytext,
  `google` tinytext,
  `linkedin` tinytext,
  `label` varchar(255) DEFAULT NULL COMMENT 'Sello en proyectos',
  `owner_background` varchar(255) DEFAULT NULL COMMENT 'Color de background módulo owner',
  `default_consultant` varchar(50) DEFAULT NULL COMMENT 'Asesor por defecto para el proyecto',
  `sponsors_limit` int(2) DEFAULT NULL COMMENT 'Número de sponsors permitidos para el canal',
  PRIMARY KEY (`id`),
  KEY `default_consultant` (`default_consultant`),
  CONSTRAINT `node_ibfk_1` FOREIGN KEY (`default_consultant`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Nodos';

/*Table structure for table `node_data` */

CREATE TABLE IF NOT EXISTS `node_data` (
  `node` varchar(50) NOT NULL,
  `projects` smallint(5) unsigned DEFAULT '0',
  `active` tinyint(3) unsigned DEFAULT '0',
  `success` smallint(5) unsigned DEFAULT '0',
  `investors` smallint(5) unsigned DEFAULT '0',
  `supporters` smallint(5) unsigned DEFAULT '0',
  `amount` mediumint(8) unsigned DEFAULT '0',
  `budget` mediumint(8) unsigned DEFAULT '0',
  `rest` mediumint(8) unsigned DEFAULT '0',
  `calls` tinyint(3) unsigned DEFAULT '0',
  `campaigns` tinyint(3) unsigned DEFAULT '0',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`node`),
  CONSTRAINT `node_data_ibfk_1` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Datos resumen nodo';

/*Table structure for table `node_lang` */

CREATE TABLE IF NOT EXISTS `node_lang` (
  `id` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `subtitle` text,
  `description` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`),
  CONSTRAINT `node_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `page` */

CREATE TABLE IF NOT EXISTS `page` (
  `id` varchar(50) NOT NULL,
  `name` tinytext NOT NULL,
  `description` text,
  `url` tinytext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Páginas institucionales';

/*Table structure for table `page_lang` */

CREATE TABLE IF NOT EXISTS `page_lang` (
  `id` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` tinytext NOT NULL,
  `description` text,
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `page_node` */

CREATE TABLE IF NOT EXISTS `page_node` (
  `page` varchar(50) NOT NULL,
  `node` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` tinytext,
  `description` text,
  `content` longtext,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `page` (`page`,`node`,`lang`),
  KEY `node` (`node`),
  CONSTRAINT `page_node_ibfk_1` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Contenidos de las paginas';

/*Table structure for table `post` */

CREATE TABLE IF NOT EXISTS `post` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `blog` bigint(20) unsigned NOT NULL,
  `title` tinytext,
  `text` longtext COMMENT 'texto de la entrada',
  `media` tinytext,
  `image` varchar(255) DEFAULT NULL COMMENT 'Contiene nombre de archivo',
  `date` date NOT NULL COMMENT 'fehca de publicacion',
  `order` int(11) DEFAULT '1',
  `allow` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Permite comentarios',
  `home` tinyint(1) DEFAULT '0' COMMENT 'para los de portada',
  `footer` tinyint(1) DEFAULT '0' COMMENT 'Para los del footer',
  `publish` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Publicado',
  `legend` text,
  `author` varchar(50) DEFAULT NULL,
  `num_comments` int(10) unsigned DEFAULT NULL COMMENT 'Número de comentarios que recibe el post',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `portada` (`home`),
  KEY `pie` (`footer`),
  KEY `publicadas` (`publish`)
) ENGINE=InnoDB AUTO_INCREMENT=5964 DEFAULT CHARSET=utf8 COMMENT='Entradas para la portada';

/*Table structure for table `post_image` */

CREATE TABLE IF NOT EXISTS `post_image` (
  `post` bigint(20) unsigned NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT 'Contiene nombre de archivo',
  PRIMARY KEY (`post`,`image`),
  CONSTRAINT `post_image_ibfk_1` FOREIGN KEY (`post`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `post_lang` */

CREATE TABLE IF NOT EXISTS `post_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `blog` int(20) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `text` longtext,
  `legend` text,
  `media` tinytext,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`),
  KEY `blog` (`blog`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `post_node` */

CREATE TABLE IF NOT EXISTS `post_node` (
  `post` bigint(20) unsigned NOT NULL,
  `node` varchar(50) NOT NULL,
  `order` int(11) DEFAULT '1',
  PRIMARY KEY (`post`,`node`),
  KEY `node` (`node`),
  CONSTRAINT `post_node_ibfk_1` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Entradas para la portada de nodos';

/*Table structure for table `post_tag` */

CREATE TABLE IF NOT EXISTS `post_tag` (
  `post` bigint(20) unsigned NOT NULL,
  `tag` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`post`,`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tags de las entradas';

/*Table structure for table `project` */

CREATE TABLE IF NOT EXISTS `project` (
  `id` varchar(50) NOT NULL,
  `name` tinytext,
  `subtitle` tinytext,
  `lang` varchar(2) DEFAULT 'es',
  `currency` varchar(4) NOT NULL DEFAULT 'EUR' COMMENT 'Divisa del proyecto',
  `currency_rate` decimal(9,5) NOT NULL DEFAULT '1.00000' COMMENT 'Ratio al crear el proyecto',
  `status` int(1) NOT NULL,
  `translate` int(1) NOT NULL DEFAULT '0',
  `progress` int(3) NOT NULL,
  `owner` varchar(50) NOT NULL COMMENT 'usuario que lo ha creado',
  `node` varchar(50) NOT NULL COMMENT 'nodo en el que se ha creado',
  `amount` int(6) DEFAULT NULL COMMENT 'acumulado actualmente',
  `mincost` int(5) DEFAULT NULL COMMENT 'minimo coste',
  `maxcost` int(5) DEFAULT NULL COMMENT 'optimo',
  `days` int(3) NOT NULL DEFAULT '0' COMMENT 'Dias restantes',
  `num_investors` int(10) unsigned DEFAULT NULL COMMENT 'Numero inversores',
  `popularity` int(10) unsigned DEFAULT NULL COMMENT 'Popularidad del proyecto',
  `num_messengers` int(10) unsigned DEFAULT NULL COMMENT 'Número de personas que envían mensajes',
  `num_posts` int(10) unsigned DEFAULT NULL COMMENT 'Número de post',
  `created` date DEFAULT NULL,
  `updated` date DEFAULT NULL,
  `published` date DEFAULT NULL,
  `success` date DEFAULT NULL,
  `closed` date DEFAULT NULL,
  `passed` date DEFAULT NULL,
  `contract_name` varchar(255) DEFAULT NULL,
  `contract_nif` varchar(15) DEFAULT NULL COMMENT 'Guardar sin espacios ni puntos ni guiones',
  `phone` varchar(20) DEFAULT NULL COMMENT 'guardar talcual',
  `contract_email` varchar(255) DEFAULT NULL,
  `address` tinytext,
  `zipcode` varchar(10) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL COMMENT 'Contiene nombre de archivo',
  `description` text,
  `motivation` text,
  `video` varchar(256) DEFAULT NULL,
  `video_usubs` int(1) NOT NULL DEFAULT '0',
  `about` text,
  `goal` text,
  `related` text,
  `spread` text,
  `reward` text,
  `category` varchar(50) DEFAULT NULL,
  `keywords` tinytext COMMENT 'Separadas por comas',
  `media` varchar(256) DEFAULT NULL,
  `media_usubs` int(1) NOT NULL DEFAULT '0',
  `currently` int(1) DEFAULT NULL,
  `project_location` varchar(256) DEFAULT NULL,
  `scope` int(1) DEFAULT NULL COMMENT 'Ambito de alcance',
  `resource` text,
  `comment` text COMMENT 'Comentario para los admin',
  `contract_entity` int(1) NOT NULL DEFAULT '0',
  `contract_birthdate` date DEFAULT NULL,
  `entity_office` varchar(255) DEFAULT NULL COMMENT 'Cargo del responsable',
  `entity_name` varchar(255) DEFAULT NULL,
  `entity_cif` varchar(10) DEFAULT NULL COMMENT 'Guardar sin espacios ni puntos ni guiones',
  `post_address` tinytext,
  `secondary_address` int(11) NOT NULL DEFAULT '0',
  `post_zipcode` varchar(10) DEFAULT NULL,
  `post_location` varchar(255) DEFAULT NULL,
  `post_country` varchar(50) DEFAULT NULL,
  `amount_users` int(10) unsigned DEFAULT NULL COMMENT 'Recaudación proveniente de los usuarios',
  `amount_call` int(10) unsigned DEFAULT NULL COMMENT 'Recaudación proveniente de la convocatoria',
  `maxproj` int(5) DEFAULT NULL COMMENT 'Dinero que puede conseguir un proyecto de la convocatoria',
  PRIMARY KEY (`id`),
  KEY `owner` (`owner`),
  KEY `nodo` (`node`),
  KEY `estado` (`status`),
  CONSTRAINT `project_ibfk_1` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `project_ibfk_2` FOREIGN KEY (`owner`) REFERENCES `user` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Proyectos de la plataforma';

/*Table structure for table `project_account` */

CREATE TABLE IF NOT EXISTS `project_account` (
  `project` varchar(50) NOT NULL,
  `bank` tinytext,
  `bank_owner` tinytext,
  `paypal` tinytext,
  `paypal_owner` tinytext,
  `allowpp` int(1) DEFAULT NULL,
  `fee` int(1) NOT NULL DEFAULT '4' COMMENT 'porcentaje de comisión goteo',
  PRIMARY KEY (`project`),
  CONSTRAINT `project_account_ibfk_1` FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Cuentas bancarias de proyecto';

/*Table structure for table `project_category` */

CREATE TABLE IF NOT EXISTS `project_category` (
  `project` varchar(50) NOT NULL,
  `category` bigint(20) unsigned NOT NULL,
  UNIQUE KEY `project_category` (`project`,`category`),
  KEY `category` (`category`),
  KEY `project` (`project`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Categorias de los proyectos';

/*Table structure for table `project_conf` */

CREATE TABLE IF NOT EXISTS `project_conf` (
  `project` varchar(50) NOT NULL,
  `noinvest` int(1) NOT NULL DEFAULT '0' COMMENT 'No se permiten más aportes',
  `watch` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Vigilar el proyecto',
  `days_round1` int(4) DEFAULT '40' COMMENT 'Días que dura la primera ronda desde la publicación del proyecto',
  `days_round2` int(4) DEFAULT '40' COMMENT 'Días que dura la segunda ronda desde la publicación del proyecto',
  `one_round` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Si el proyecto tiene una unica ronda',
  `help_license` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Si necesita ayuda en licencias',
  `help_cost` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Si necesita ayuda en costes',
  PRIMARY KEY (`project`),
  CONSTRAINT `project_conf_ibfk_1` FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Configuraciones para proyectos';

/*Table structure for table `project_data` */

CREATE TABLE IF NOT EXISTS `project_data` (
  `project` varchar(50) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `invested` int(6) unsigned NOT NULL DEFAULT '0' COMMENT 'Mostrado en termometro al cerrar',
  `fee` int(6) unsigned NOT NULL DEFAULT '0' COMMENT 'comisiones cobradas por bancos y paypal a goteo',
  `issue` int(6) unsigned NOT NULL DEFAULT '0' COMMENT 'importe de las incidencias',
  `amount` int(6) unsigned NOT NULL DEFAULT '0' COMMENT 'recaudaro realmente',
  `goteo` int(6) unsigned NOT NULL DEFAULT '0' COMMENT 'comision goteo',
  `percent` int(1) unsigned NOT NULL DEFAULT '8' COMMENT 'porcentaje comision goteo',
  `comment` text COMMENT 'comentarios y/o listado de incidencias',
  PRIMARY KEY (`project`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='datos de informe financiero';

/*Table structure for table `project_image` */

CREATE TABLE IF NOT EXISTS `project_image` (
  `project` varchar(50) NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT 'Contiene nombre de archivo',
  `section` varchar(50) DEFAULT NULL,
  `url` tinytext,
  `order` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`project`,`image`),
  KEY `proyecto-seccion` (`project`,`section`),
  CONSTRAINT `project_image_ibfk_1` FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `project_lang` */

CREATE TABLE IF NOT EXISTS `project_lang` (
  `id` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `description` text,
  `motivation` text,
  `video` varchar(256) DEFAULT NULL,
  `about` text,
  `goal` text,
  `related` text,
  `reward` text,
  `keywords` tinytext,
  `media` varchar(255) DEFAULT NULL,
  `subtitle` tinytext,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `project_location` */

CREATE TABLE IF NOT EXISTS `project_location` (
  `id` varchar(50) NOT NULL,
  `latitude` decimal(16,14) NOT NULL,
  `longitude` decimal(16,14) NOT NULL,
  `method` varchar(50) NOT NULL DEFAULT 'ip',
  `locable` tinyint(1) NOT NULL DEFAULT '0',
  `city` varchar(255) NOT NULL,
  `region` varchar(255) NOT NULL,
  `country` varchar(150) NOT NULL,
  `country_code` varchar(2) NOT NULL,
  `info` varchar(255) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `locable` (`locable`),
  CONSTRAINT `project_location_ibfk_1` FOREIGN KEY (`id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `project_open_tag` */

CREATE TABLE IF NOT EXISTS `project_open_tag` (
  `project` varchar(50) NOT NULL,
  `open_tag` int(12) NOT NULL,
  UNIQUE KEY `project_open_tag` (`project`,`open_tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Agrupacion de los proyectos';

/*Table structure for table `promote` */

CREATE TABLE IF NOT EXISTS `promote` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `node` varchar(50) NOT NULL,
  `project` varchar(50) NOT NULL,
  `title` tinytext,
  `description` text,
  `order` smallint(5) unsigned NOT NULL DEFAULT '1',
  `active` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_node` (`node`,`project`),
  UNIQUE KEY `id` (`id`),
  KEY `activos` (`active`),
  KEY `project` (`project`),
  CONSTRAINT `promote_ibfk_1` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `promote_ibfk_2` FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=921 DEFAULT CHARSET=utf8 COMMENT='Proyectos destacados';

/*Table structure for table `promote_lang` */

CREATE TABLE IF NOT EXISTS `promote_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `description` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`),
  CONSTRAINT `promote_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `promote` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `review` */

CREATE TABLE IF NOT EXISTS `review` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `to_checker` text,
  `to_owner` text,
  `score` int(2) NOT NULL DEFAULT '0',
  `max` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=284 DEFAULT CHARSET=utf8 COMMENT='Revision para evaluacion de proyecto';

/*Table structure for table `review_comment` */

CREATE TABLE IF NOT EXISTS `review_comment` (
  `review` bigint(20) unsigned NOT NULL,
  `user` varchar(50) NOT NULL,
  `section` varchar(50) NOT NULL,
  `evaluation` text,
  `recommendation` text,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`review`,`user`,`section`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Comentarios de revision';

/*Table structure for table `review_score` */

CREATE TABLE IF NOT EXISTS `review_score` (
  `review` bigint(20) unsigned NOT NULL,
  `user` varchar(50) NOT NULL,
  `criteria` bigint(20) unsigned NOT NULL,
  `score` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`review`,`user`,`criteria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Puntuacion por citerio';

/*Table structure for table `reward` */

CREATE TABLE IF NOT EXISTS `reward` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project` varchar(50) NOT NULL,
  `reward` tinytext,
  `description` text,
  `type` varchar(50) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `other` tinytext COMMENT 'Otro tipo de recompensa',
  `license` varchar(50) DEFAULT NULL,
  `amount` int(5) DEFAULT NULL,
  `units` int(5) DEFAULT NULL,
  `fulsocial` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Retorno colectivo cumplido',
  `url` tinytext COMMENT 'Localización del Retorno cumplido',
  `order` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Orden para retornos colectivos',
  `bonus` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Retorno colectivo adicional',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `project` (`project`),
  KEY `icon` (`icon`),
  KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=25710 DEFAULT CHARSET=utf8 COMMENT='Retornos colectivos e individuales';

/*Table structure for table `reward_lang` */

CREATE TABLE IF NOT EXISTS `reward_lang` (
  `id` int(20) NOT NULL,
  `project` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `reward` tinytext,
  `description` text,
  `other` tinytext,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`),
  KEY `project` (`project`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `role` */

CREATE TABLE IF NOT EXISTS `role` (
  `id` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `sponsor` */

CREATE TABLE IF NOT EXISTS `sponsor` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `url` tinytext,
  `image` varchar(255) DEFAULT NULL COMMENT 'Contiene nombre de archivo',
  `order` int(11) NOT NULL DEFAULT '1',
  `node` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `node` (`node`),
  CONSTRAINT `sponsor_ibfk_1` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COMMENT='Patrocinadores';

/*Table structure for table `stories` */

CREATE TABLE IF NOT EXISTS `stories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `node` varchar(50) NOT NULL,
  `project` varchar(50) DEFAULT NULL,
  `order` smallint(5) unsigned NOT NULL DEFAULT '1',
  `image` varchar(255) DEFAULT NULL COMMENT 'Contiene nombre de archivo',
  `active` int(1) NOT NULL DEFAULT '0',
  `title` tinytext,
  `description` text,
  `review` text,
  `url` tinytext,
  `post` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `node` (`node`),
  CONSTRAINT `stories_ibfk_1` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='Historias existosas';

/*Table structure for table `stories_lang` */

CREATE TABLE IF NOT EXISTS `stories_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `description` text,
  `review` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `support` */

CREATE TABLE IF NOT EXISTS `support` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project` varchar(50) NOT NULL,
  `support` tinytext,
  `description` text,
  `type` varchar(50) DEFAULT NULL,
  `thread` bigint(20) unsigned DEFAULT NULL COMMENT 'De la tabla message',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `hilo` (`thread`),
  KEY `proyecto` (`project`)
) ENGINE=InnoDB AUTO_INCREMENT=6143 DEFAULT CHARSET=utf8 COMMENT='Colaboraciones';

/*Table structure for table `support_lang` */

CREATE TABLE IF NOT EXISTS `support_lang` (
  `id` int(20) NOT NULL,
  `project` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `support` tinytext,
  `description` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`),
  KEY `project` (`project`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `tag` */

CREATE TABLE IF NOT EXISTS `tag` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 COMMENT='Tags de blogs (de nodo)';

/*Table structure for table `tag_lang` */

CREATE TABLE IF NOT EXISTS `tag_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` tinytext,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `task` */

CREATE TABLE IF NOT EXISTS `task` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `node` varchar(50) NOT NULL,
  `text` text NOT NULL,
  `url` tinytext,
  `done` varchar(50) DEFAULT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `node` (`node`),
  CONSTRAINT `task_ibfk_1` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tareas pendientes de admin';

/*Table structure for table `template` */

CREATE TABLE IF NOT EXISTS `template` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `group` varchar(50) NOT NULL DEFAULT 'general' COMMENT 'Agrupación de uso',
  `purpose` tinytext NOT NULL,
  `title` tinytext NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8 COMMENT='Plantillas emails automáticos';

/*Table structure for table `template_lang` */

CREATE TABLE IF NOT EXISTS `template_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `text` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  PRIMARY KEY (`id`,`lang`),
  CONSTRAINT `template_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `text` */

CREATE TABLE IF NOT EXISTS `text` (
  `id` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL DEFAULT 'es',
  `text` text NOT NULL,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  PRIMARY KEY (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Textos multi-idioma';

/*Table structure for table `user` */

CREATE TABLE IF NOT EXISTS `user` (
  `id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(40) NOT NULL,
  `about` text,
  `keywords` tinytext,
  `active` tinyint(1) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL COMMENT 'Contiene nombre de archivo',
  `contribution` text,
  `twitter` tinytext,
  `facebook` tinytext,
  `google` tinytext,
  `identica` tinytext,
  `linkedin` tinytext,
  `amount` int(7) DEFAULT NULL COMMENT 'Cantidad total aportada',
  `num_patron` int(10) unsigned DEFAULT NULL COMMENT 'Num. proyectos patronizados',
  `num_patron_active` int(10) unsigned DEFAULT NULL COMMENT 'Num. proyectos patronizados activos',
  `worth` int(7) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `token` tinytext NOT NULL,
  `hide` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'No se ve publicamente',
  `confirmed` int(1) NOT NULL DEFAULT '0',
  `lang` varchar(2) DEFAULT 'es',
  `node` varchar(50) DEFAULT NULL,
  `num_invested` int(10) unsigned DEFAULT NULL COMMENT 'Num. proyectos cofinanciados',
  `num_owned` int(10) unsigned DEFAULT NULL COMMENT 'Num. proyectos publicados',
  PRIMARY KEY (`id`),
  KEY `nodo` (`node`),
  KEY `coordenadas` (`location`),
  CONSTRAINT `user_ibfk_1` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `user_api` */

CREATE TABLE IF NOT EXISTS `user_api` (
  `user_id` varchar(50) NOT NULL,
  `key` varchar(50) NOT NULL,
  `expiration_date` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `user_api_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `user_donation` */

CREATE TABLE IF NOT EXISTS `user_donation` (
  `user` varchar(50) NOT NULL,
  `amount` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `surname` varchar(255) DEFAULT NULL COMMENT 'Apellido',
  `nif` varchar(12) DEFAULT NULL,
  `address` tinytext,
  `zipcode` varchar(10) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL COMMENT 'Provincia',
  `country` varchar(50) DEFAULT NULL,
  `countryname` varchar(255) DEFAULT NULL COMMENT 'Nombre del pais',
  `numproj` int(2) DEFAULT '1',
  `year` varchar(4) NOT NULL,
  `edited` int(1) DEFAULT '0' COMMENT 'Revisados por el usuario',
  `confirmed` int(1) DEFAULT '0' COMMENT 'Certificado generado',
  `pdf` varchar(255) DEFAULT NULL COMMENT 'nombre del archivo de certificado',
  PRIMARY KEY (`user`,`year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Datos fiscales donativo';

/*Table structure for table `user_interest` */

CREATE TABLE IF NOT EXISTS `user_interest` (
  `user` varchar(50) NOT NULL,
  `interest` int(12) NOT NULL,
  UNIQUE KEY `user_interest` (`user`,`interest`),
  KEY `usuario` (`user`),
  KEY `interes` (`interest`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Intereses de los usuarios';

/*Table structure for table `user_lang` */

CREATE TABLE IF NOT EXISTS `user_lang` (
  `id` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `about` text,
  `keywords` tinytext,
  `contribution` text,
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `user_location` */

CREATE TABLE IF NOT EXISTS `user_location` (
  `id` varchar(50) NOT NULL,
  `latitude` decimal(16,14) NOT NULL,
  `longitude` decimal(16,14) NOT NULL,
  `method` varchar(50) NOT NULL DEFAULT 'ip',
  `locable` tinyint(1) NOT NULL DEFAULT '0',
  `city` varchar(255) NOT NULL,
  `region` varchar(255) NOT NULL,
  `country` varchar(150) NOT NULL,
  `country_code` varchar(2) NOT NULL,
  `info` varchar(255) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `locable` (`locable`),
  CONSTRAINT `user_location_ibfk_1` FOREIGN KEY (`id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `user_login` */

CREATE TABLE IF NOT EXISTS `user_login` (
  `user` varchar(50) NOT NULL,
  `provider` varchar(50) NOT NULL,
  `oauth_token` text NOT NULL,
  `oauth_token_secret` text NOT NULL,
  `datetime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user`,`oauth_token`(255)),
  CONSTRAINT `user_login_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `user_node` */

CREATE TABLE IF NOT EXISTS `user_node` (
  `user` varchar(50) NOT NULL,
  `node` varchar(50) NOT NULL,
  PRIMARY KEY (`user`,`node`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `user_personal` */

CREATE TABLE IF NOT EXISTS `user_personal` (
  `user` varchar(50) NOT NULL,
  `contract_name` varchar(255) DEFAULT NULL,
  `contract_surname` varchar(255) DEFAULT NULL,
  `contract_nif` varchar(15) DEFAULT NULL COMMENT 'Guardar sin espacios ni puntos ni guiones',
  `contract_email` varchar(256) DEFAULT NULL,
  `phone` varchar(9) DEFAULT NULL COMMENT 'guardar sin espacios ni puntos',
  `address` tinytext,
  `zipcode` varchar(10) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Datos personales de usuario';

/*Table structure for table `user_pool` */

CREATE TABLE IF NOT EXISTS `user_pool` (
  `user` varchar(50) NOT NULL,
  `amount` int(7) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user`),
  CONSTRAINT `user_pool_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `user_prefer` */

CREATE TABLE IF NOT EXISTS `user_prefer` (
  `user` varchar(50) NOT NULL,
  `updates` int(1) NOT NULL DEFAULT '0',
  `threads` int(1) NOT NULL DEFAULT '0',
  `rounds` int(1) NOT NULL DEFAULT '0',
  `mailing` int(1) NOT NULL DEFAULT '0',
  `email` int(1) NOT NULL DEFAULT '0',
  `tips` int(1) NOT NULL DEFAULT '0',
  `comlang` varchar(2) DEFAULT NULL,
  `currency` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Preferencias de notificacion de usuario';

/*Table structure for table `user_project` */

CREATE TABLE IF NOT EXISTS `user_project` (
  `user` varchar(50) NOT NULL,
  `project` varchar(50) NOT NULL,
  UNIQUE KEY `user` (`user`,`project`),
  KEY `project` (`project`),
  CONSTRAINT `user_project_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_project_ibfk_2` FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `user_review` */

CREATE TABLE IF NOT EXISTS `user_review` (
  `user` varchar(50) NOT NULL,
  `review` bigint(20) unsigned NOT NULL,
  `ready` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Ha terminado con la revision',
  PRIMARY KEY (`user`,`review`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Asignacion de revision a usuario';

/*Table structure for table `user_role` */

CREATE TABLE IF NOT EXISTS `user_role` (
  `user_id` varchar(50) NOT NULL,
  `role_id` varchar(50) NOT NULL,
  `node_id` varchar(50) DEFAULT NULL,
  `datetime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `user_FK` (`user_id`),
  KEY `role_FK` (`role_id`),
  KEY `node_FK` (`node_id`),
  CONSTRAINT `user_role_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_role_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_role_ibfk_3` FOREIGN KEY (`node_id`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `user_translang` */

CREATE TABLE IF NOT EXISTS `user_translang` (
  `user` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  PRIMARY KEY (`user`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Idiomas de traductores';

/*Table structure for table `user_translate` */

CREATE TABLE IF NOT EXISTS `user_translate` (
  `user` varchar(50) NOT NULL,
  `type` varchar(10) NOT NULL COMMENT 'Tipo de contenido',
  `item` varchar(50) NOT NULL COMMENT 'id del contenido',
  `ready` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Ha terminado con la traduccion',
  PRIMARY KEY (`user`,`type`,`item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Asignacion de traduccion a usuario';

/*Table structure for table `user_vip` */

CREATE TABLE IF NOT EXISTS `user_vip` (
  `user` varchar(50) NOT NULL,
  `image` varchar(255) DEFAULT NULL COMMENT 'Contiene nombre de archivo',
  PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Datos usuario colaborador';

/*Table structure for table `user_web` */

CREATE TABLE IF NOT EXISTS `user_web` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(50) NOT NULL,
  `url` tinytext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13917 DEFAULT CHARSET=utf8 COMMENT='Webs de los usuarios';

/*Table structure for table `worthcracy` */

CREATE TABLE IF NOT EXISTS `worthcracy` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `amount` int(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='Niveles de meritocracia';

/*Table structure for table `worthcracy_lang` */

CREATE TABLE IF NOT EXISTS `worthcracy_lang` (
  `id` int(2) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` tinytext NOT NULL,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS=1;
