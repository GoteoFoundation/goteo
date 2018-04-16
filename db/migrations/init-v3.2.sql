/* Inits SQL to version 3.2 (after that SQL upgrades will be managed by console migrate script) */

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

/*Table structure for table `banner` */

CREATE TABLE `banner` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Proyectos en banner superior';

/*Table structure for table `banner_lang` */

CREATE TABLE `banner_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `description` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `blog` */

CREATE TABLE `blog` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(10) NOT NULL,
  `owner` varchar(50) NOT NULL COMMENT 'la id del proyecto o nodo',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Blogs de nodo o proyecto';

/*Table structure for table `call` */

CREATE TABLE `call` (
  `id` varchar(50) NOT NULL,
  `name` tinytext,
  `subtitle` text,
  `lang` varchar(2) NOT NULL DEFAULT 'es',
  `status` int(1) NOT NULL,
  `translate` int(1) NOT NULL DEFAULT '0',
  `owner` varchar(50) NOT NULL COMMENT 'entidad que convoca',
  `amount` int(6) NOT NULL COMMENT 'presupuesto',
  `created` date DEFAULT NULL,
  `updated` date DEFAULT NULL,
  `opened` date DEFAULT NULL,
  `published` date DEFAULT NULL,
  `success` date DEFAULT NULL,
  `closed` date DEFAULT NULL,
  `contract_name` varchar(255) DEFAULT NULL,
  `contract_nif` varchar(10) DEFAULT NULL COMMENT 'Guardar sin espacios ni puntos ni guiones',
  `phone` varchar(20) DEFAULT NULL COMMENT 'guardar sin espacios ni puntos',
  `contract_email` varchar(255) DEFAULT NULL,
  `address` tinytext,
  `zipcode` varchar(10) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL COMMENT 'Logo. Contiene nombre de archivo',
  `image` varchar(255) DEFAULT NULL COMMENT 'Imagen widget. Contiene nombre de archivo',
  `backimage` varchar(255) DEFAULT NULL COMMENT 'Imagen background. Contiene nombre de archivo',
  `description` longtext,
  `description_summary` text,
  `description_nav` text,
  `whom` text,
  `apply` text,
  `legal` longtext,
  `dossier` tinytext,
  `tweet` tinytext,
  `fbappid` tinytext,
  `call_location` varchar(256) DEFAULT NULL,
  `resources` text COMMENT 'Recursos de capital riego',
  `scope` int(1) NOT NULL,
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
  `days` int(2) DEFAULT NULL,
  `maxdrop` int(6) DEFAULT NULL COMMENT 'Riego maximo por aporte',
  `modemaxp` varchar(3) DEFAULT 'imp' COMMENT 'Modalidad del máximo por proyecto: imp = importe, per = porcentaje',
  `maxproj` int(6) NOT NULL COMMENT 'Riego maximo por proyecto',
  `num_projects` int(10) unsigned NOT NULL COMMENT 'Número de proyectos publicados',
  `rest` int(10) unsigned NOT NULL COMMENT 'Importe riego disponible',
  `used` int(10) unsigned NOT NULL COMMENT 'Importe riego comprometido',
  `applied` int(10) unsigned NOT NULL COMMENT 'Número de proyectos aplicados',
  `running_projects` int(10) unsigned NOT NULL COMMENT 'Número de proyectos en campaña',
  `success_projects` int(10) unsigned NOT NULL COMMENT 'Número de proyectos exitosos',
  `fee_projects_drop` int(2) NOT NULL DEFAULT '4' COMMENT 'Fee to apply in the financial report to the drop',
  `facebook_pixel` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `owner` (`owner`),
  CONSTRAINT `call_ibfk_1` FOREIGN KEY (`owner`) REFERENCES `user` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Convocatorias';

/*Table structure for table `call_banner` */

CREATE TABLE `call_banner` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `call` varchar(50) NOT NULL,
  `name` tinytext NOT NULL,
  `url` tinytext,
  `image` varchar(255) DEFAULT NULL COMMENT 'Contiene nombre de archivo',
  `order` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `call` (`call`),
  CONSTRAINT `call_banner_ibfk_1` FOREIGN KEY (`call`) REFERENCES `call` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Banners de convocatorias';

/*Table structure for table `call_banner_lang` */

CREATE TABLE `call_banner_lang` (
  `id` int(11) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` tinytext,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  PRIMARY KEY (`id`,`lang`),
  UNIQUE KEY `id_lang` (`id`,`lang`),
  CONSTRAINT `call_banner_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `call_banner` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `call_category` */

CREATE TABLE `call_category` (
  `call` varchar(50) NOT NULL,
  `category` int(10) unsigned NOT NULL,
  UNIQUE KEY `call_category` (`call`,`category`),
  KEY `category` (`category`),
  CONSTRAINT `call_category_ibfk_1` FOREIGN KEY (`call`) REFERENCES `call` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `call_category_ibfk_2` FOREIGN KEY (`category`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Categorias de las convocatorias';

/*Table structure for table `call_conf` */

CREATE TABLE `call_conf` (
  `call` varchar(50) NOT NULL,
  `applied` int(4) DEFAULT NULL COMMENT 'Para fijar numero de proyectos recibidos',
  `limit1` set('normal','minimum','unlimited','none') NOT NULL DEFAULT 'normal' COMMENT 'tipo limite riego primera ronda',
  `limit2` set('normal','minimum','unlimited','none') NOT NULL DEFAULT 'none' COMMENT 'tipo limite riego segunda ronda',
  `buzz_first` int(1) NOT NULL DEFAULT '0' COMMENT 'Solo primer hashtag en el buzz',
  `buzz_own` int(1) NOT NULL DEFAULT '1' COMMENT 'Tweets  propios en el buzz',
  `buzz_mention` int(1) NOT NULL DEFAULT '1' COMMENT 'Menciones en el buzz',
  `map_stage1` varchar(256) DEFAULT NULL COMMENT 'Map iframe for stage 1',
  `map_stage2` varchar(256) DEFAULT NULL COMMENT 'Map iframe for stage 2',
  `date_stage1` date DEFAULT NULL COMMENT 'Stage 1 date',
  `date_stage1_out` date DEFAULT NULL COMMENT 'Stage 1 date out',
  `date_stage2` date DEFAULT NULL COMMENT 'Stage 2 date',
  `date_stage3` date DEFAULT NULL COMMENT 'Stage 3 date',
  PRIMARY KEY (`call`),
  CONSTRAINT `call_conf_ibfk_1` FOREIGN KEY (`call`) REFERENCES `call` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Configuración de convocatoria';

/*Table structure for table `call_icon` */

CREATE TABLE `call_icon` (
  `call` varchar(50) NOT NULL,
  `icon` varchar(50) NOT NULL,
  UNIQUE KEY `call_icon` (`call`,`icon`),
  KEY `icon` (`icon`),
  CONSTRAINT `call_icon_ibfk_1` FOREIGN KEY (`call`) REFERENCES `call` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `call_icon_ibfk_2` FOREIGN KEY (`icon`) REFERENCES `icon` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tipos de retorno de las convocatorias';

/*Table structure for table `call_lang` */

CREATE TABLE `call_lang` (
  `id` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` tinytext,
  `description` longtext,
  `description_summary` text,
  `description_nav` text,
  `whom` text,
  `apply` text,
  `legal` longtext,
  `subtitle` text,
  `dossier` tinytext,
  `tweet` tinytext,
  `resources` text COMMENT 'Recursos de capital riego',
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`),
  CONSTRAINT `call_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `call` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `call_location` */

CREATE TABLE `call_location` (
  `id` varchar(50) NOT NULL,
  `latitude` decimal(16,14) NOT NULL,
  `longitude` decimal(16,14) NOT NULL,
  `radius` smallint(6) unsigned NOT NULL DEFAULT '0',
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
  CONSTRAINT `call_location_ibfk_1` FOREIGN KEY (`id`) REFERENCES `call` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `call_post` */

CREATE TABLE `call_post` (
  `call` varchar(50) NOT NULL,
  `post` bigint(20) unsigned NOT NULL,
  UNIQUE KEY `call_post` (`call`,`post`),
  KEY `post` (`post`),
  CONSTRAINT `call_post_ibfk_1` FOREIGN KEY (`call`) REFERENCES `call` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `call_post_ibfk_2` FOREIGN KEY (`post`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Entradas de blog asignadas a convocatorias';

/*Table structure for table `call_project` */

CREATE TABLE `call_project` (
  `call` varchar(50) NOT NULL,
  `project` varchar(50) NOT NULL,
  UNIQUE KEY `call_project` (`call`,`project`),
  KEY `call_project_ibfk_2` (`project`),
  CONSTRAINT `call_project_ibfk_1` FOREIGN KEY (`call`) REFERENCES `call` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `call_project_ibfk_2` FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Proyectos asignados a convocatorias';

/*Table structure for table `call_sphere` */

CREATE TABLE `call_sphere` (
  `call` varchar(50) NOT NULL,
  `sphere` bigint(20) unsigned NOT NULL,
  `order` smallint(5) unsigned NOT NULL DEFAULT '1',
  UNIQUE KEY `call_sphere` (`call`,`sphere`),
  KEY `sphere` (`sphere`),
  CONSTRAINT `call_sphere_ibfk_1` FOREIGN KEY (`call`) REFERENCES `call` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `call_sphere_ibfk_2` FOREIGN KEY (`sphere`) REFERENCES `sphere` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Ámbito de convocatorias';

/*Table structure for table `call_sponsor` */

CREATE TABLE `call_sponsor` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `call` varchar(50) NOT NULL,
  `name` tinytext NOT NULL,
  `url` tinytext,
  `image` varchar(255) DEFAULT NULL COMMENT 'Contiene nombre de archivo',
  `order` int(11) NOT NULL DEFAULT '1',
  `amount` int(11) DEFAULT NULL,
  `main` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Sponsor main',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `call` (`call`),
  CONSTRAINT `call_sponsor_ibfk_1` FOREIGN KEY (`call`) REFERENCES `call` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Patrocinadores de convocatorias';

/*Table structure for table `campaign` */

CREATE TABLE `campaign` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `node` varchar(50) NOT NULL,
  `call` varchar(50) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '0',
  `order` smallint(5) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `call_node` (`node`,`call`),
  KEY `call` (`call`),
  CONSTRAINT `campaign_ibfk_1` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `campaign_ibfk_2` FOREIGN KEY (`call`) REFERENCES `call` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Convocatorias en portada';

/*Table structure for table `category` */

CREATE TABLE `category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext,
  `description` text,
  `order` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `social_commitment` varchar(50) DEFAULT NULL COMMENT 'Social commitment',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='Categorias de los proyectos';

/*Table structure for table `category_lang` */

CREATE TABLE `category_lang` (
  `id` int(10) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` tinytext,
  `description` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  PRIMARY KEY (`id`,`lang`),
  KEY `lang` (`lang`),
  CONSTRAINT `category_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `comment` */

CREATE TABLE `comment` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post` bigint(20) unsigned NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `text` text NOT NULL,
  `user` varchar(50) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Comentarios';

/*Table structure for table `conf` */

CREATE TABLE `conf` (
  `key` varchar(255) NOT NULL COMMENT 'Clave',
  `value` varchar(255) NOT NULL COMMENT 'Valor'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Para guardar pares para configuraciones, bloqueos etc';

/*Table structure for table `contract` */

CREATE TABLE `contract` (
  `project` varchar(50) NOT NULL,
  `number` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL COMMENT 'dia anterior a la publicacion',
  `enddate` date NOT NULL COMMENT 'finalización, un año despues de la fecha de contrato',
  `pdf` varchar(255) DEFAULT NULL COMMENT 'Archivo pdf contrato',
  `type` varchar(1) NOT NULL DEFAULT '0' COMMENT '0 = persona física; 1 = representante asociacion; 2 = apoderado entidad mercantil',
  `name` tinytext,
  `nif` varchar(14) DEFAULT NULL,
  `office` tinytext COMMENT 'Cargo en la asociación o empresa',
  `address` tinytext,
  `location` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `zipcode` varchar(8) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `entity_name` tinytext,
  `entity_cif` varchar(10) DEFAULT NULL,
  `entity_address` tinytext,
  `entity_location` varchar(255) DEFAULT NULL,
  `entity_region` varchar(255) DEFAULT NULL,
  `entity_zipcode` varchar(8) DEFAULT NULL,
  `entity_country` varchar(50) DEFAULT NULL,
  `reg_name` tinytext COMMENT 'Nombre y ciudad del registro en el que esta inscrita la entidad',
  `reg_date` date DEFAULT NULL,
  `reg_number` tinytext COMMENT 'Número de registro',
  `reg_loc` tinytext COMMENT 'NO SE USA (borrar)',
  `reg_id` tinytext COMMENT 'Número de protocolo del notario',
  `reg_idname` tinytext COMMENT 'Nombre del notario',
  `reg_idloc` tinytext COMMENT 'Ciudad de actuación del notario',
  `project_name` tinytext COMMENT 'Nombre del proyecto',
  `project_url` varchar(255) DEFAULT NULL COMMENT 'URL del proyecto',
  `project_owner` tinytext COMMENT 'Nombre del impulsor',
  `project_user` tinytext COMMENT 'Nombre del usuario autor del proyecto',
  `project_profile` varchar(255) DEFAULT NULL COMMENT 'URL del perfil del autor del proyecto',
  `project_description` text COMMENT 'Breve descripción del proyecto',
  `project_invest` text COMMENT 'objetivo del crowdfunding',
  `project_return` text COMMENT 'retornos',
  `bank` tinytext,
  `bank_owner` tinytext,
  `paypal` tinytext,
  `paypal_owner` tinytext,
  `birthdate` date DEFAULT NULL,
  PRIMARY KEY (`project`),
  UNIQUE KEY `numero` (`number`),
  CONSTRAINT `contract_ibfk_1` FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Contratos';

/*Table structure for table `contract_status` */

CREATE TABLE `contract_status` (
  `contract` varchar(50) NOT NULL COMMENT 'Id del proyecto',
  `owner` int(1) NOT NULL DEFAULT '0' COMMENT 'El impulsor ha dado por rellenados los datos',
  `owner_date` date DEFAULT NULL COMMENT 'Fecha que se cambia el flag',
  `owner_user` varchar(50) DEFAULT NULL COMMENT 'Usuario que cambia el flag',
  `admin` int(1) NOT NULL DEFAULT '0' COMMENT 'El admin ha comenzado a revisar los datos',
  `admin_date` date DEFAULT NULL COMMENT 'Fecha que se cambia el flag',
  `admin_user` varchar(50) DEFAULT NULL COMMENT 'Usuario que cambia el flag',
  `ready` int(1) NOT NULL DEFAULT '0' COMMENT 'Datos verificados y correctos',
  `ready_date` date DEFAULT NULL COMMENT 'Fecha que se cambia el flag',
  `ready_user` varchar(50) DEFAULT NULL COMMENT 'Usuario que cambia el flag',
  `pdf` int(1) NOT NULL COMMENT 'El impulsor ha descargado el pdf',
  `pdf_date` date DEFAULT NULL COMMENT 'Fecha que se cambia el flag',
  `pdf_user` varchar(50) DEFAULT NULL COMMENT 'Usuario que cambia el flag',
  `received` int(1) NOT NULL DEFAULT '0' COMMENT 'Se ha recibido el contrato firmado',
  `received_date` date DEFAULT NULL COMMENT 'Fecha que se cambia el flag',
  `received_user` varchar(50) DEFAULT NULL COMMENT 'Usuario que cambia el flag',
  `payed` int(1) NOT NULL DEFAULT '0' COMMENT 'Se ha realizado el pago al proyecto',
  `payed_date` date DEFAULT NULL COMMENT 'Fecha que se cambia el flag',
  `payed_user` varchar(50) DEFAULT NULL COMMENT 'Usuario que cambia el flag',
  `prepay` int(1) NOT NULL DEFAULT '0' COMMENT 'Ha habido pago avanzado',
  `prepay_date` date DEFAULT NULL COMMENT 'Fecha que se cambia el flag',
  `prepay_user` varchar(50) DEFAULT NULL COMMENT 'Usuario que cambia el flag',
  `closed` int(1) NOT NULL DEFAULT '0' COMMENT 'Contrato finiquitado',
  `closed_date` date DEFAULT NULL COMMENT 'Fecha que se cambia el flag',
  `closed_user` varchar(50) DEFAULT NULL COMMENT 'Usuario que cambia el flag',
  PRIMARY KEY (`contract`),
  KEY `owner_user` (`owner_user`),
  KEY `admin_user` (`admin_user`),
  KEY `pdf_user` (`pdf_user`),
  KEY `payed_user` (`payed_user`),
  KEY `prepay_user` (`prepay_user`),
  KEY `closed_user` (`closed_user`),
  KEY `ready_user` (`ready_user`),
  KEY `received_user` (`received_user`),
  CONSTRAINT `contract_status_ibfk_1` FOREIGN KEY (`contract`) REFERENCES `contract` (`project`) ON UPDATE CASCADE,
  CONSTRAINT `contract_status_ibfk_2` FOREIGN KEY (`owner_user`) REFERENCES `user` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `contract_status_ibfk_3` FOREIGN KEY (`admin_user`) REFERENCES `user` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `contract_status_ibfk_4` FOREIGN KEY (`pdf_user`) REFERENCES `user` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `contract_status_ibfk_5` FOREIGN KEY (`payed_user`) REFERENCES `user` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `contract_status_ibfk_6` FOREIGN KEY (`prepay_user`) REFERENCES `user` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `contract_status_ibfk_7` FOREIGN KEY (`closed_user`) REFERENCES `user` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `contract_status_ibfk_8` FOREIGN KEY (`ready_user`) REFERENCES `user` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `contract_status_ibfk_9` FOREIGN KEY (`received_user`) REFERENCES `user` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Seguimiento de estado de contrato';

/*Table structure for table `cost` */

CREATE TABLE `cost` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project` varchar(50) NOT NULL,
  `cost` tinytext,
  `description` text,
  `type` varchar(50) DEFAULT NULL,
  `amount` int(5) DEFAULT '0',
  `required` tinyint(1) DEFAULT '0',
  `from` date DEFAULT NULL,
  `until` date DEFAULT NULL,
  `order` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `order` (`order`),
  KEY `project` (`project`),
  CONSTRAINT `cost_ibfk_1` FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Desglose de costes de proyectos';

/*Table structure for table `cost_lang` */

CREATE TABLE `cost_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `project` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `cost` tinytext,
  `description` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`),
  KEY `project` (`project`),
  CONSTRAINT `cost_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `cost` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cost_lang_ibfk_2` FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `criteria` */

CREATE TABLE `criteria` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `section` varchar(50) NOT NULL DEFAULT 'node',
  `title` tinytext,
  `description` text,
  `order` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COMMENT='Criterios de puntuación';

/*Table structure for table `criteria_lang` */

CREATE TABLE `criteria_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `description` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `document` */

CREATE TABLE `document` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contract` varchar(50) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(120) DEFAULT NULL,
  `size` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contract` (`contract`),
  CONSTRAINT `document_ibfk_1` FOREIGN KEY (`contract`) REFERENCES `contract` (`project`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `donor` */

CREATE TABLE `donor` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(50) NOT NULL,
  `amount` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `surname` varchar(255) DEFAULT NULL COMMENT 'Apellido',
  `surname2` char(255) DEFAULT NULL,
  `nif` varchar(12) DEFAULT NULL,
  `address` tinytext,
  `zipcode` varchar(10) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL COMMENT 'Provincia',
  `country` varchar(50) DEFAULT NULL,
  `countryname` varchar(255) DEFAULT NULL COMMENT 'Nombre del pais',
  `gender` char(1) DEFAULT NULL,
  `birthyear` year(4) DEFAULT NULL,
  `numproj` int(2) DEFAULT '1',
  `year` varchar(4) NOT NULL,
  `edited` int(1) DEFAULT '0' COMMENT 'Revisados por el usuario',
  `confirmed` int(1) DEFAULT '0' COMMENT 'Certificado generado',
  `pdf` varchar(255) DEFAULT NULL COMMENT 'nombre del archivo de certificado',
  `created` datetime DEFAULT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `year` (`year`),
  CONSTRAINT `donor_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Datos fiscales donativo';

/*Table structure for table `donor_invest` */

CREATE TABLE `donor_invest` (
  `donor_id` bigint(20) unsigned NOT NULL,
  `invest_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`donor_id`,`invest_id`),
  KEY `invest_id` (`invest_id`),
  CONSTRAINT `donor_invest_ibfk_1` FOREIGN KEY (`donor_id`) REFERENCES `donor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `donor_invest_ibfk_2` FOREIGN KEY (`invest_id`) REFERENCES `invest` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `donor_location` */

CREATE TABLE `donor_location` (
  `id` bigint(20) unsigned NOT NULL,
  `latitude` decimal(16,14) NOT NULL,
  `longitude` decimal(16,14) NOT NULL,
  `radius` smallint(6) unsigned NOT NULL DEFAULT '0',
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
  CONSTRAINT `donor_location_ibfk_1` FOREIGN KEY (`id`) REFERENCES `donor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `event` */

CREATE TABLE `event` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` char(20) NOT NULL DEFAULT 'communication',
  `action` char(100) NOT NULL,
  `hash` char(32) NOT NULL,
  `result` char(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `finalized` datetime DEFAULT NULL,
  `succeeded` tinyint(1) DEFAULT '0',
  `error` char(255) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `hash` (`hash`),
  KEY `succeeded` (`succeeded`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `faq` */

CREATE TABLE `faq` (
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
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8 COMMENT='Preguntas frecuentes';

/*Table structure for table `faq_lang` */

CREATE TABLE `faq_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `description` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `feed` */

CREATE TABLE `feed` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Log de eventos';

/*Table structure for table `glossary` */

CREATE TABLE `glossary` (
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

CREATE TABLE `glossary_image` (
  `glossary` bigint(20) unsigned NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT 'Contiene nombre de archivo',
  `order` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`glossary`,`image`),
  CONSTRAINT `glossary_image_ibfk_1` FOREIGN KEY (`glossary`) REFERENCES `glossary` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `glossary_lang` */

CREATE TABLE `glossary_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `text` longtext,
  `legend` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `home` */

CREATE TABLE `home` (
  `item` varchar(10) NOT NULL,
  `type` varchar(5) NOT NULL DEFAULT 'main' COMMENT 'lateral o central',
  `node` varchar(50) NOT NULL,
  `order` smallint(5) unsigned NOT NULL DEFAULT '1',
  UNIQUE KEY `item_node` (`item`,`node`),
  KEY `node` (`node`),
  CONSTRAINT `home_ibfk_1` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Elementos en portada';

/*Table structure for table `icon` */

CREATE TABLE `icon` (
  `id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` tinytext,
  `group` varchar(50) DEFAULT NULL COMMENT 'exclusivo para grupo',
  `order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Iconos para retorno/recompensa';

/*Table structure for table `icon_lang` */

CREATE TABLE `icon_lang` (
  `id` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` tinytext,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `icon_license` */

CREATE TABLE `icon_license` (
  `icon` varchar(50) NOT NULL,
  `license` varchar(50) NOT NULL,
  UNIQUE KEY `icon` (`icon`,`license`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Licencias para cada icono, solo social';

/*Table structure for table `image` */

CREATE TABLE `image` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `size` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36969 DEFAULT CHARSET=utf8;

/*Table structure for table `info` */

CREATE TABLE `info` (
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

CREATE TABLE `info_image` (
  `info` bigint(20) unsigned NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT 'Contiene nombre de archivo',
  `order` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`info`,`image`),
  CONSTRAINT `info_image_ibfk_1` FOREIGN KEY (`info`) REFERENCES `info` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `info_lang` */

CREATE TABLE `info_lang` (
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

CREATE TABLE `invest` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(50) NOT NULL,
  `project` varchar(50) DEFAULT NULL,
  `account` varchar(256) NOT NULL COMMENT 'Solo para aportes de cash',
  `amount` int(6) NOT NULL,
  `amount_original` int(6) DEFAULT NULL COMMENT 'Importe introducido por el usuario',
  `currency` varchar(4) NOT NULL DEFAULT 'EUR' COMMENT 'Divisa al aportar',
  `currency_rate` decimal(9,5) NOT NULL DEFAULT '1.00000' COMMENT 'Ratio de conversión a eurio al aportar',
  `status` int(1) NOT NULL COMMENT '-1 en proceso, 0 pendiente, 1 cobrado, 2 devuelto, 3 pagado al proyecto',
  `anonymous` tinyint(1) NOT NULL DEFAULT '0',
  `resign` tinyint(1) NOT NULL DEFAULT '0',
  `invested` date DEFAULT NULL,
  `charged` date DEFAULT NULL,
  `returned` date DEFAULT NULL,
  `preapproval` varchar(256) DEFAULT NULL COMMENT 'PreapprovalKey',
  `payment` varchar(256) DEFAULT NULL COMMENT 'PayKey',
  `transaction` varchar(256) DEFAULT NULL COMMENT 'PaypalId',
  `method` varchar(20) NOT NULL COMMENT 'Metodo de pago',
  `admin` varchar(50) DEFAULT NULL COMMENT 'Admin que creó el aporte manual',
  `campaign` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'si es un aporte de capital riego',
  `datetime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `drops` bigint(20) unsigned DEFAULT NULL COMMENT 'id del aporte que provoca este riego',
  `droped` bigint(20) unsigned DEFAULT NULL COMMENT 'id del riego generado por este aporte',
  `call` varchar(50) DEFAULT NULL COMMENT 'campaña dedonde sale el dinero',
  `matcher` varchar(50) DEFAULT NULL,
  `issue` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Problemas con el cobro del aporte',
  `pool` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'A reservar si el proyecto falla',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `usuario` (`user`),
  KEY `proyecto` (`project`),
  KEY `convocatoria` (`call`),
  KEY `matcher` (`matcher`),
  CONSTRAINT `invest_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `invest_ibfk_2` FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `invest_ibfk_3` FOREIGN KEY (`call`) REFERENCES `call` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `invest_ibfk_4` FOREIGN KEY (`matcher`) REFERENCES `matcher` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Aportes monetarios a proyectos';

/*Table structure for table `invest_address` */

CREATE TABLE `invest_address` (
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

CREATE TABLE `invest_detail` (
  `invest` bigint(20) unsigned NOT NULL,
  `type` varchar(30) NOT NULL,
  `log` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `invest_type` (`invest`,`type`),
  KEY `invest` (`invest`),
  CONSTRAINT `invest_detail_ibfk_1` FOREIGN KEY (`invest`) REFERENCES `invest` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Detalles de los aportes';

/*Table structure for table `invest_location` */

CREATE TABLE `invest_location` (
  `id` bigint(20) unsigned NOT NULL,
  `latitude` decimal(16,14) NOT NULL,
  `longitude` decimal(16,14) NOT NULL,
  `radius` smallint(6) unsigned NOT NULL DEFAULT '0',
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
  CONSTRAINT `invest_location_ibfk_1` FOREIGN KEY (`id`) REFERENCES `invest` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `invest_msg` */

CREATE TABLE `invest_msg` (
  `invest` bigint(20) unsigned NOT NULL,
  `msg` text,
  PRIMARY KEY (`invest`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Mensaje de apoyo al proyecto tras aportar';

/*Table structure for table `invest_node` */

CREATE TABLE `invest_node` (
  `user_id` varchar(50) NOT NULL,
  `user_node` varchar(50) NOT NULL,
  `project_id` varchar(50) DEFAULT NULL,
  `project_node` varchar(50) DEFAULT NULL,
  `invest_id` bigint(20) unsigned NOT NULL,
  `invest_node` varchar(50) NOT NULL COMMENT 'Nodo en el que se hace el aporte',
  UNIQUE KEY `invest` (`invest_id`),
  KEY `invest_id` (`invest_id`),
  KEY `invest_node` (`invest_node`),
  KEY `project_id` (`project_id`),
  KEY `project_node` (`project_node`),
  KEY `user_id` (`user_id`),
  KEY `user_node` (`user_node`),
  CONSTRAINT `invest_node_ibfk_1` FOREIGN KEY (`user_node`) REFERENCES `node` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `invest_node_ibfk_2` FOREIGN KEY (`project_node`) REFERENCES `node` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `invest_node_ibfk_3` FOREIGN KEY (`invest_node`) REFERENCES `node` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `invest_node_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `invest_node_ibfk_5` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `invest_node_ibfk_6` FOREIGN KEY (`invest_id`) REFERENCES `invest` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Aportes por usuario/nodo a proyecto/nodo';

/*Table structure for table `invest_reward` */

CREATE TABLE `invest_reward` (
  `invest` bigint(20) unsigned NOT NULL,
  `reward` bigint(20) unsigned NOT NULL,
  `fulfilled` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `invest` (`invest`,`reward`),
  KEY `reward` (`reward`),
  CONSTRAINT `invest_reward_ibfk_1` FOREIGN KEY (`invest`) REFERENCES `invest` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `invest_reward_ibfk_2` FOREIGN KEY (`reward`) REFERENCES `reward` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Recompensas elegidas al aportar';

/*Table structure for table `license` */

CREATE TABLE `license` (
  `id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `group` varchar(50) DEFAULT NULL COMMENT 'grupo de restriccion de menor a mayor',
  `url` varchar(256) DEFAULT NULL,
  `order` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Licencias de distribucion';

/*Table structure for table `license_lang` */

CREATE TABLE `license_lang` (
  `id` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text,
  `url` varchar(256) DEFAULT NULL,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `log` */

CREATE TABLE `log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `scope` varchar(50) NOT NULL,
  `target_type` varchar(10) DEFAULT NULL COMMENT 'tipo de objetivo',
  `target_id` varchar(50) DEFAULT NULL COMMENT 'registro objetivo',
  `text` text,
  `url` tinytext,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Log de cosas';

/*Table structure for table `mail` */

CREATE TABLE `mail` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` char(255) NOT NULL,
  `subject` char(255) DEFAULT NULL,
  `content` longtext NOT NULL,
  `template` varchar(100) DEFAULT NULL,
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
  CONSTRAINT `mail_ibfk_2` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Contenido enviado por email para el -si no ves-';

/*Table structure for table `mail_stats` */

CREATE TABLE `mail_stats` (
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
  KEY `mail_id` (`mail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `mail_stats_location` */

CREATE TABLE `mail_stats_location` (
  `id` bigint(20) unsigned NOT NULL,
  `latitude` decimal(16,14) NOT NULL,
  `longitude` decimal(16,14) NOT NULL,
  `radius` smallint(6) unsigned NOT NULL DEFAULT '0',
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
  KEY `locable` (`locable`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `mailer_content` */

CREATE TABLE `mailer_content` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Contenido a enviar';

/*Table structure for table `mailer_control` */

CREATE TABLE `mailer_control` (
  `email` char(150) NOT NULL,
  `bounces` int(10) unsigned NOT NULL,
  `complaints` int(10) unsigned NOT NULL,
  `action` enum('allow','deny') DEFAULT 'allow',
  `last_reason` char(255) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Lista negra para bounces y complaints';

/*Table structure for table `mailer_limit` */

CREATE TABLE `mailer_limit` (
  `hora` time NOT NULL COMMENT 'Hora envio',
  `num` int(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Cuantos',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`hora`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Para limitar el número de envios diarios';

/*Table structure for table `mailer_send` */

CREATE TABLE `mailer_send` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Destinatarios pendientes y realizados';

/*Table structure for table `matcher` */

CREATE TABLE `matcher` (
  `id` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `lang` varchar(2) NOT NULL,
  `owner` varchar(50) NOT NULL,
  `terms` longtext,
  `processor` varchar(50) NOT NULL DEFAULT '' COMMENT 'ID for the MatcherProcessor that handles the logic of this matcher',
  `vars` text NOT NULL COMMENT 'Customizable vars to be used in the processor',
  `amount` int(10) unsigned NOT NULL DEFAULT '0',
  `used` int(10) unsigned NOT NULL DEFAULT '0',
  `crowd` int(10) unsigned NOT NULL DEFAULT '0',
  `projects` int(10) unsigned NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` date DEFAULT NULL,
  `modified_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `owner` (`owner`),
  CONSTRAINT `matcher_ibfk_1` FOREIGN KEY (`owner`) REFERENCES `user` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `matcher_project` */

CREATE TABLE `matcher_project` (
  `matcher_id` varchar(50) NOT NULL,
  `project_id` varchar(50) NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'pending' COMMENT 'pending, accepted, active (funding ok), rejected (discarded by user), discarded (by admin)',
  PRIMARY KEY (`matcher_id`,`project_id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `matcher_project_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `matcher_project_ibfk_2` FOREIGN KEY (`matcher_id`) REFERENCES `matcher` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `matcher_user` */

CREATE TABLE `matcher_user` (
  `matcher_id` varchar(50) NOT NULL COMMENT 'Matcher campaign',
  `user_id` varchar(50) NOT NULL COMMENT 'User owner',
  `pool` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Use owner''s pool as funding source',
  PRIMARY KEY (`matcher_id`,`user_id`),
  KEY `matcher_user_ibfk_1` (`user_id`),
  CONSTRAINT `matcher_user_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `matcher_user_ibfk_2` FOREIGN KEY (`matcher_id`) REFERENCES `matcher` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `message` */

CREATE TABLE `message` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(50) NOT NULL,
  `project` varchar(50) NOT NULL,
  `thread` bigint(20) unsigned DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `message` text NOT NULL,
  `blocked` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'No se puede modificar ni borrar',
  `closed` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'No se puede responder',
  `private` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `thread` (`thread`),
  KEY `user` (`user`),
  KEY `project` (`project`),
  CONSTRAINT `message_ibfk_1` FOREIGN KEY (`thread`) REFERENCES `message` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `message_ibfk_2` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `message_ibfk_3` FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Mensajes de usuarios en proyecto';

/*Table structure for table `message_lang` */

CREATE TABLE `message_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `message` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`),
  CONSTRAINT `message_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `message` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `message_user` */

CREATE TABLE `message_user` (
  `message_id` bigint(20) unsigned NOT NULL,
  `user_id` char(50) NOT NULL,
  PRIMARY KEY (`message_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `message_user_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `message` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `message_user_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `metric` */

CREATE TABLE `metric` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `metric` char(255) NOT NULL,
  `desc` char(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `metric` (`metric`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `milestone` */

CREATE TABLE `milestone` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL,
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `image_emoji` varchar(255) DEFAULT NULL,
  `twitter_msg` text,
  `facebook_msg` text,
  `twitter_msg_owner` text,
  `facebook_msg_owner` text,
  `link` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Milestones';

/*Table structure for table `milestone_lang` */

CREATE TABLE `milestone_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `twitter_msg` text,
  `facebook_msg` text,
  `twitter_msg_owner` text,
  `facebook_msg_owner` text,
  `pending` int(1) DEFAULT '0',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `news` */

CREATE TABLE `news` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Noticias en la cabecera';

/*Table structure for table `news_lang` */

CREATE TABLE `news_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `description` text,
  `url` tinytext,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `node` */

CREATE TABLE `node` (
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
  `home_img` varchar(255) DEFAULT NULL COMMENT 'Imagen para módulo canales en home',
  `owner_font_color` varchar(255) DEFAULT NULL COMMENT 'Color de fuente módulo owner',
  `owner_social_color` varchar(255) DEFAULT NULL COMMENT 'Color de iconos sociales módulo owner',
  PRIMARY KEY (`id`),
  KEY `default_consultant` (`default_consultant`),
  CONSTRAINT `node_ibfk_1` FOREIGN KEY (`default_consultant`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Nodos';

/*Table structure for table `node_data` */

CREATE TABLE `node_data` (
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

CREATE TABLE `node_lang` (
  `id` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `subtitle` text,
  `description` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`),
  CONSTRAINT `node_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `open_tag` */

CREATE TABLE `open_tag` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext,
  `description` text,
  `order` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `post` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Agrupacion de los proyectos';

/*Table structure for table `open_tag_lang` */

CREATE TABLE `open_tag_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` tinytext,
  `description` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `origin` */

CREATE TABLE `origin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tag` char(50) NOT NULL,
  `category` char(50) NOT NULL,
  `type` enum('referer','ua') NOT NULL COMMENT 'referer, ua',
  `project_id` char(50) DEFAULT NULL,
  `invest_id` bigint(20) unsigned DEFAULT NULL,
  `call_id` char(50) DEFAULT NULL,
  `counter` int(10) unsigned NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `modified_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project` (`tag`,`project_id`,`type`,`category`),
  KEY `project_id` (`project_id`),
  KEY `invest_id` (`invest_id`),
  KEY `call_id` (`call_id`),
  KEY `call` (`tag`,`category`,`type`,`call_id`),
  KEY `invest` (`tag`,`category`,`type`,`invest_id`),
  CONSTRAINT `origin_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `origin_ibfk_2` FOREIGN KEY (`invest_id`) REFERENCES `invest` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `origin_ibfk_3` FOREIGN KEY (`call_id`) REFERENCES `call` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=171760 DEFAULT CHARSET=utf8;

/*Table structure for table `page` */

CREATE TABLE `page` (
  `id` varchar(50) NOT NULL,
  `name` tinytext NOT NULL,
  `description` text,
  `type` char(20) NOT NULL DEFAULT 'html',
  `url` tinytext,
  `content` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Páginas institucionales';

/*Table structure for table `page_lang` */

CREATE TABLE `page_lang` (
  `id` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` tinytext NOT NULL,
  `description` text,
  `content` longtext,
  `pending` tinyint(1) DEFAULT NULL,
  UNIQUE KEY `id_lang` (`id`,`lang`),
  CONSTRAINT `page_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `page` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `patron` */

CREATE TABLE `patron` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `node` varchar(50) NOT NULL,
  `project` varchar(50) NOT NULL,
  `user` varchar(50) NOT NULL,
  `title` tinytext,
  `description` text,
  `link` tinytext,
  `order` smallint(5) unsigned NOT NULL DEFAULT '1',
  `active` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_project_node` (`node`,`project`,`user`),
  KEY `project` (`project`),
  CONSTRAINT `patron_ibfk_1` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `patron_ibfk_2` FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Proyectos recomendados por padrinos';

/*Table structure for table `patron_lang` */

CREATE TABLE `patron_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `description` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `patron_order` */

CREATE TABLE `patron_order` (
  `id` varchar(50) NOT NULL,
  `order` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Orden de los padrinos';

/*Table structure for table `post` */

CREATE TABLE `post` (
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
  KEY `publicadas` (`publish`),
  KEY `post_ibfk_1` (`blog`),
  CONSTRAINT `post_ibfk_1` FOREIGN KEY (`blog`) REFERENCES `blog` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Entradas para la portada';

/*Table structure for table `post_image` */

CREATE TABLE `post_image` (
  `post` bigint(20) unsigned NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT 'Contiene nombre de archivo',
  `order` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`post`,`image`),
  CONSTRAINT `post_image_ibfk_1` FOREIGN KEY (`post`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `post_lang` */

CREATE TABLE `post_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `blog` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `text` longtext,
  `legend` text,
  `media` tinytext,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`),
  KEY `blog` (`blog`),
  CONSTRAINT `post_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `post_lang_ibfk_2` FOREIGN KEY (`blog`) REFERENCES `blog` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `post_node` */

CREATE TABLE `post_node` (
  `post` bigint(20) unsigned NOT NULL,
  `node` varchar(50) NOT NULL,
  `order` int(11) DEFAULT '1',
  PRIMARY KEY (`post`,`node`),
  KEY `node` (`node`),
  CONSTRAINT `post_node_ibfk_1` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `post_node_ibfk_2` FOREIGN KEY (`post`) REFERENCES `post` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Entradas para la portada de nodos';

/*Table structure for table `post_tag` */

CREATE TABLE `post_tag` (
  `post` bigint(20) unsigned NOT NULL,
  `tag` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`post`,`tag`),
  KEY `tag` (`tag`),
  CONSTRAINT `post_tag_ibfk_1` FOREIGN KEY (`post`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `post_tag_ibfk_2` FOREIGN KEY (`tag`) REFERENCES `tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tags de las entradas';

/*Table structure for table `project` */

CREATE TABLE `project` (
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
  `analytics_id` varchar(30) DEFAULT NULL,
  `facebook_pixel` varchar(20) DEFAULT NULL,
  `social_commitment` varchar(50) DEFAULT NULL COMMENT 'Social commitment of the project',
  `social_commitment_description` text COMMENT 'Social commitment of the project',
  `execution_plan` text,
  `sustainability_model` text,
  `execution_plan_url` tinytext,
  `sustainability_model_url` tinytext,
  PRIMARY KEY (`id`),
  KEY `owner` (`owner`),
  KEY `nodo` (`node`),
  KEY `estado` (`status`),
  CONSTRAINT `project_ibfk_1` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `project_ibfk_2` FOREIGN KEY (`owner`) REFERENCES `user` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Proyectos de la plataforma';

/*Table structure for table `project_account` */

CREATE TABLE `project_account` (
  `project` varchar(50) NOT NULL,
  `bank` tinytext,
  `bank_owner` tinytext,
  `paypal` tinytext,
  `paypal_owner` tinytext,
  `allowpp` int(1) DEFAULT NULL,
  `fee` int(1) NOT NULL DEFAULT '4' COMMENT 'porcentaje de comisión goteo',
  `vat` int(2) NOT NULL DEFAULT '21' COMMENT '(Value Added Tax) to apply in the financial report',
  `skip_login` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`project`),
  CONSTRAINT `project_account_ibfk_1` FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Cuentas bancarias de proyecto';

/*Table structure for table `project_category` */

CREATE TABLE `project_category` (
  `project` varchar(50) NOT NULL,
  `category` int(10) unsigned NOT NULL,
  UNIQUE KEY `project_category` (`project`,`category`),
  KEY `category` (`category`),
  KEY `project` (`project`),
  CONSTRAINT `project_category_ibfk_1` FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `project_category_ibfk_2` FOREIGN KEY (`category`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Categorias de los proyectos';

/*Table structure for table `project_conf` */

CREATE TABLE `project_conf` (
  `project` varchar(50) NOT NULL,
  `noinvest` int(1) NOT NULL DEFAULT '0' COMMENT 'No se permiten más aportes',
  `watch` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Vigilar el proyecto',
  `days_round1` int(4) DEFAULT '40' COMMENT 'Días que dura la primera ronda desde la publicación del proyecto',
  `days_round2` int(4) DEFAULT '40' COMMENT 'Días que dura la segunda ronda desde la publicación del proyecto',
  `one_round` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Si el proyecto tiene una unica ronda',
  `help_license` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Si necesita ayuda en licencias',
  `help_cost` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Si necesita ayuda en costes',
  `mincost_estimation` int(11) DEFAULT NULL,
  `publishing_estimation` date DEFAULT NULL,
  PRIMARY KEY (`project`),
  CONSTRAINT `project_conf_ibfk_1` FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Configuraciones para proyectos';

/*Table structure for table `project_data` */

CREATE TABLE `project_data` (
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

CREATE TABLE `project_image` (
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

CREATE TABLE `project_lang` (
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
  `social_commitment_description` text COMMENT 'Social commitment of the project',
  UNIQUE KEY `id_lang` (`id`,`lang`),
  CONSTRAINT `project_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `project_location` */

CREATE TABLE `project_location` (
  `id` varchar(50) NOT NULL,
  `latitude` decimal(16,14) NOT NULL,
  `longitude` decimal(16,14) NOT NULL,
  `radius` smallint(6) NOT NULL DEFAULT '0',
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

/*Table structure for table `project_milestone` */

CREATE TABLE `project_milestone` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project` varchar(50) NOT NULL,
  `milestone` bigint(20) unsigned DEFAULT NULL,
  `date` date DEFAULT NULL,
  `post` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `project` (`project`),
  KEY `milestone` (`milestone`),
  KEY `post` (`post`),
  CONSTRAINT `project_milestone_ibfk_1` FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `project_milestone_ibfk_2` FOREIGN KEY (`milestone`) REFERENCES `milestone` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `project_milestone_ibfk_3` FOREIGN KEY (`post`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Project milestones';

/*Table structure for table `project_open_tag` */

CREATE TABLE `project_open_tag` (
  `project` varchar(50) NOT NULL,
  `open_tag` int(12) NOT NULL,
  UNIQUE KEY `project_open_tag` (`project`,`open_tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Agrupacion de los proyectos';

/*Table structure for table `promote` */

CREATE TABLE `promote` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Proyectos destacados';

/*Table structure for table `promote_lang` */

CREATE TABLE `promote_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `description` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`),
  CONSTRAINT `promote_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `promote` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `relief` */

CREATE TABLE `relief` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `year` int(4) NOT NULL,
  `percentage` int(2) NOT NULL,
  `country` varchar(10) DEFAULT NULL,
  `limit_amount` int(10) NOT NULL,
  `type` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `year` (`year`,`country`,`limit_amount`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Desgravaciones fiscales';

/*Table structure for table `review` */

CREATE TABLE `review` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `to_checker` text,
  `to_owner` text,
  `score` int(2) NOT NULL DEFAULT '0',
  `max` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `project` (`project`),
  CONSTRAINT `review_ibfk_1` FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Revision para evaluacion de proyecto';

/*Table structure for table `review_comment` */

CREATE TABLE `review_comment` (
  `review` bigint(20) unsigned NOT NULL,
  `user` varchar(50) NOT NULL,
  `section` varchar(50) NOT NULL,
  `evaluation` text,
  `recommendation` text,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`review`,`user`,`section`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Comentarios de revision';

/*Table structure for table `review_score` */

CREATE TABLE `review_score` (
  `review` bigint(20) unsigned NOT NULL,
  `user` varchar(50) NOT NULL,
  `criteria` bigint(20) unsigned NOT NULL,
  `score` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`review`,`user`,`criteria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Puntuacion por citerio';

/*Table structure for table `reward` */

CREATE TABLE `reward` (
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
  `category` varchar(50) DEFAULT NULL COMMENT 'Category social impact',
  PRIMARY KEY (`id`),
  KEY `project` (`project`),
  KEY `icon` (`icon`),
  KEY `type` (`type`),
  KEY `order` (`order`),
  CONSTRAINT `reward_ibfk_1` FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Retornos colectivos e individuales';

/*Table structure for table `reward_lang` */

CREATE TABLE `reward_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `project` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `reward` tinytext,
  `description` text,
  `other` tinytext,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`),
  KEY `project` (`project`),
  CONSTRAINT `reward_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `reward` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `reward_lang_ibfk_2` FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `role` */

CREATE TABLE `role` (
  `id` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `social_commitment` */

CREATE TABLE `social_commitment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(255) NOT NULL,
  `description` text NOT NULL,
  `image` char(255) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Compromiso social';

/*Table structure for table `social_commitment_lang` */

CREATE TABLE `social_commitment_lang` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lang` varchar(2) NOT NULL,
  `name` char(255) NOT NULL,
  `description` text NOT NULL,
  `pending` int(1) DEFAULT '0' COMMENT 'To be reviewed',
  UNIQUE KEY `id_lang` (`id`,`lang`),
  CONSTRAINT `social_commitment_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `social_commitment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `sphere` */

CREATE TABLE `sphere` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Ámbitos de convocatorias';

/*Table structure for table `sphere_lang` */

CREATE TABLE `sphere_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` text,
  `pending` int(1) DEFAULT '0',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `sponsor` */

CREATE TABLE `sponsor` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `url` tinytext,
  `image` varchar(255) DEFAULT NULL COMMENT 'Contiene nombre de archivo',
  `order` int(11) NOT NULL DEFAULT '1',
  `node` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `node` (`node`),
  CONSTRAINT `sponsor_ibfk_1` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Patrocinadores';

/*Table structure for table `stories` */

CREATE TABLE `stories` (
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
  `pool_image` varchar(255) DEFAULT NULL,
  `pool` int(1) NOT NULL DEFAULT '0',
  `text_position` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `node` (`node`),
  CONSTRAINT `stories_ibfk_1` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Historias existosas';

/*Table structure for table `stories_lang` */

CREATE TABLE `stories_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `description` text,
  `review` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `support` */

CREATE TABLE `support` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project` varchar(50) NOT NULL,
  `support` tinytext,
  `description` text,
  `type` varchar(50) DEFAULT NULL,
  `thread` bigint(20) unsigned DEFAULT NULL COMMENT 'De la tabla message',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `hilo` (`thread`),
  KEY `proyecto` (`project`),
  CONSTRAINT `support_ibfk_1` FOREIGN KEY (`thread`) REFERENCES `message` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `support_ibfk_2` FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Colaboraciones';

/*Table structure for table `support_lang` */

CREATE TABLE `support_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `project` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `support` tinytext,
  `description` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`),
  KEY `project` (`project`),
  CONSTRAINT `support_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `support` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `support_lang_ibfk_2` FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `tag` */

CREATE TABLE `tag` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tags de blogs (de nodo)';

/*Table structure for table `tag_lang` */

CREATE TABLE `tag_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` tinytext,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `task` */

CREATE TABLE `task` (
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

CREATE TABLE `template` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `group` varchar(50) NOT NULL DEFAULT 'general' COMMENT 'Agrupación de uso',
  `purpose` tinytext NOT NULL,
  `title` tinytext NOT NULL,
  `text` text NOT NULL,
  `type` char(20) NOT NULL DEFAULT 'html',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8 COMMENT='Plantillas emails automáticos';

/*Table structure for table `template_lang` */

CREATE TABLE `template_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `text` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  PRIMARY KEY (`id`,`lang`),
  CONSTRAINT `template_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `text` */

CREATE TABLE `text` (
  `id` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL DEFAULT 'es',
  `text` text NOT NULL,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  PRIMARY KEY (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Textos multi-idioma';

/*Table structure for table `user` */

CREATE TABLE `user` (
  `id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `gender` char(1) DEFAULT NULL,
  `birthyear` year(4) DEFAULT NULL,
  `entity_type` tinyint(1) DEFAULT NULL,
  `legal_entity` tinyint(1) DEFAULT NULL,
  `about` text,
  `keywords` tinytext,
  `active` tinyint(1) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL COMMENT 'Contiene nombre de archivo',
  `contribution` text,
  `twitter` tinytext,
  `facebook` tinytext,
  `google` tinytext,
  `instagram` tinytext,
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

CREATE TABLE `user_api` (
  `user_id` varchar(50) NOT NULL,
  `key` varchar(50) NOT NULL,
  `expiration_date` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `user_api_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `user_call` */

CREATE TABLE `user_call` (
  `user` varchar(50) NOT NULL,
  `call` varchar(50) NOT NULL,
  PRIMARY KEY (`user`,`call`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Asignacion de convocatorias a admines';

/*Table structure for table `user_donation` */

CREATE TABLE `user_donation` (
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

/*Table structure for table `user_favourite_project` */

CREATE TABLE `user_favourite_project` (
  `user` varchar(50) NOT NULL,
  `project` varchar(50) NOT NULL,
  `date_send` date DEFAULT NULL,
  `date_marked` date DEFAULT NULL,
  UNIQUE KEY `user_favourite_project` (`user`,`project`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='User favourites projects';

/*Table structure for table `user_interest` */

CREATE TABLE `user_interest` (
  `user` varchar(50) NOT NULL,
  `interest` int(10) unsigned NOT NULL,
  UNIQUE KEY `user_interest` (`user`,`interest`),
  KEY `usuario` (`user`),
  KEY `interes` (`interest`),
  CONSTRAINT `user_interest_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_interest_ibfk_2` FOREIGN KEY (`interest`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Intereses de los usuarios';

/*Table structure for table `user_lang` */

CREATE TABLE `user_lang` (
  `id` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `about` text,
  `name` varchar(100) DEFAULT NULL,
  `keywords` tinytext,
  `contribution` text,
  UNIQUE KEY `id_lang` (`id`,`lang`),
  CONSTRAINT `user_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `user_location` */

CREATE TABLE `user_location` (
  `id` varchar(50) NOT NULL,
  `latitude` decimal(16,14) NOT NULL,
  `longitude` decimal(16,14) NOT NULL,
  `radius` smallint(6) unsigned NOT NULL DEFAULT '0',
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

CREATE TABLE `user_login` (
  `user` varchar(50) NOT NULL,
  `provider` varchar(50) NOT NULL,
  `oauth_token` text NOT NULL,
  `oauth_token_secret` text NOT NULL,
  `datetime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user`,`oauth_token`(255)),
  CONSTRAINT `user_login_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `user_node` */

CREATE TABLE `user_node` (
  `user` varchar(50) NOT NULL,
  `node` varchar(50) NOT NULL,
  PRIMARY KEY (`user`,`node`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `user_personal` */

CREATE TABLE `user_personal` (
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

CREATE TABLE `user_pool` (
  `user` varchar(50) NOT NULL,
  `amount` int(7) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user`),
  CONSTRAINT `user_pool_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `user_prefer` */

CREATE TABLE `user_prefer` (
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

CREATE TABLE `user_project` (
  `user` varchar(50) NOT NULL,
  `project` varchar(50) NOT NULL,
  UNIQUE KEY `user` (`user`,`project`),
  KEY `project` (`project`),
  CONSTRAINT `user_project_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_project_ibfk_2` FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `user_review` */

CREATE TABLE `user_review` (
  `user` varchar(50) NOT NULL,
  `review` bigint(20) unsigned NOT NULL,
  `ready` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Ha terminado con la revision',
  PRIMARY KEY (`user`,`review`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Asignacion de revision a usuario';

/*Table structure for table `user_role` */

CREATE TABLE `user_role` (
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

CREATE TABLE `user_translang` (
  `user` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  PRIMARY KEY (`user`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Idiomas de traductores';

/*Table structure for table `user_translate` */

CREATE TABLE `user_translate` (
  `user` varchar(50) NOT NULL,
  `type` varchar(10) NOT NULL COMMENT 'Tipo de contenido',
  `item` varchar(50) NOT NULL COMMENT 'id del contenido',
  `ready` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Ha terminado con la traduccion',
  PRIMARY KEY (`user`,`type`,`item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Asignacion de traduccion a usuario';

/*Table structure for table `user_vip` */

CREATE TABLE `user_vip` (
  `user` varchar(50) NOT NULL,
  `image` varchar(255) DEFAULT NULL COMMENT 'Contiene nombre de archivo',
  PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Datos usuario colaborador';

/*Table structure for table `user_web` */

CREATE TABLE `user_web` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(50) NOT NULL,
  `url` tinytext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Webs de los usuarios';

/*Table structure for table `worthcracy` */

CREATE TABLE `worthcracy` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `amount` int(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Niveles de meritocracia';

/*Table structure for table `worthcracy_lang` */

CREATE TABLE `worthcracy_lang` (
  `id` int(2) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` tinytext NOT NULL,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
