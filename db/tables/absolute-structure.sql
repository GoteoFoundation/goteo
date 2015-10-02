CREATE TABLE `acl` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `node_id` varchar(50) NOT NULL,
  `role_id` varchar(50) DEFAULT NULL,
  `user_id` varchar(50) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `allow` tinyint(1) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `role_FK` (`role_id`),
  KEY `user_FK` (`user_id`),
  KEY `node_FK` (`node_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Proyectos en banner superior';

CREATE TABLE `banner_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `description` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `bazar` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reward` bigint(20) unsigned DEFAULT NULL,
  `project` varchar(50) DEFAULT NULL,
  `title` tinytext,
  `description` text,
  `amount` int(5) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL COMMENT 'Contiene nombre de archivo',
  `order` smallint(5) NOT NULL DEFAULT '9999',
  `active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='productos del catalogo';

CREATE TABLE `bazar_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `description` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `blog` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(10) NOT NULL,
  `owner` varchar(50) NOT NULL COMMENT 'la id del proyecto o nodo',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Blogs de nodo o proyecto';

CREATE TABLE `call` (
  `id` varchar(50) NOT NULL,
  `name` tinytext,
  `subtitle` tinytext,
  `lang` varchar(2) NOT NULL DEFAULT 'es',
  `status` int(1) NOT NULL,
  `translate` int(1) NOT NULL DEFAULT '0',
  `owner` varchar(50) NOT NULL COMMENT 'entidad que convoca',
  `amount` int(6) DEFAULT NULL COMMENT 'presupuesto',
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
  `description` text,
  `whom` text,
  `apply` text,
  `legal` longtext,
  `dossier` tinytext,
  `tweet` tinytext,
  `fbappid` tinytext,
  `call_location` varchar(256) DEFAULT NULL,
  `resources` text COMMENT 'Recursos de capital riego',
  `scope` int(1) DEFAULT NULL,
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
  `maxproj` int(6) DEFAULT NULL COMMENT 'Riego maximo por proyecto',
  `num_projects` int(10) unsigned DEFAULT NULL COMMENT 'Número de proyectos publicados',
  `rest` int(10) unsigned DEFAULT NULL COMMENT 'Importe riego disponible',
  `used` int(10) unsigned DEFAULT NULL COMMENT 'Importe riego comprometido',
  `applied` int(10) unsigned DEFAULT NULL COMMENT 'Número de proyectos aplicados',
  `running_projects` int(10) unsigned DEFAULT NULL COMMENT 'Número de proyectos en campaña',
  `success_projects` int(10) unsigned DEFAULT NULL COMMENT 'Número de proyectos exitosos',
  PRIMARY KEY (`id`),
  KEY `owner` (`owner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Convocatorias';

CREATE TABLE `call_banner` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `call` varchar(50) NOT NULL,
  `name` tinytext NOT NULL,
  `url` tinytext,
  `image` varchar(255) DEFAULT NULL COMMENT 'Contiene nombre de archivo',
  `order` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Banners de convocatorias';

CREATE TABLE `call_banner_lang` (
  `id` int(20) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` tinytext,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `call_category` (
  `call` varchar(50) NOT NULL,
  `category` int(12) NOT NULL,
  UNIQUE KEY `call_category` (`call`,`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Categorias de las convocatorias';

CREATE TABLE `call_conf` (
  `call` varchar(50) NOT NULL,
  `applied` int(4) DEFAULT NULL COMMENT 'Para fijar numero de proyectos recibidos',
  `limit1` set('normal','minimum','unlimited','none') NOT NULL DEFAULT 'normal' COMMENT 'tipo limite riego primera ronda',
  `limit2` set('normal','minimum','unlimited','none') NOT NULL DEFAULT 'none' COMMENT 'tipo limite riego segunda ronda',
  `buzz_first` int(1) NOT NULL DEFAULT '0' COMMENT 'Solo primer hashtag en el buzz',
  `buzz_own` int(1) NOT NULL DEFAULT '1' COMMENT 'Tweets  propios en el buzz',
  `buzz_mention` int(1) NOT NULL DEFAULT '1' COMMENT 'Menciones en el buzz',
  PRIMARY KEY (`call`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Configuración de convocatoria';

CREATE TABLE `call_icon` (
  `call` varchar(50) NOT NULL,
  `icon` varchar(50) NOT NULL,
  UNIQUE KEY `call_icon` (`call`,`icon`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tipos de retorno de las convocatorias';

CREATE TABLE `call_lang` (
  `id` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` tinytext,
  `description` text,
  `whom` text,
  `apply` text,
  `legal` longtext,
  `subtitle` text,
  `dossier` tinytext,
  `tweet` tinytext,
  `resources` text COMMENT 'Recursos de capital riego',
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `call_post` (
  `call` varchar(50) NOT NULL,
  `post` int(20) NOT NULL,
  UNIQUE KEY `call_post` (`call`,`post`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Entradas de blog asignadas a convocatorias';

CREATE TABLE `call_project` (
  `call` varchar(50) NOT NULL,
  `project` varchar(50) NOT NULL,
  UNIQUE KEY `call_project` (`call`,`project`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Proyectos asignados a convocatorias';

CREATE TABLE `call_sponsor` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `call` varchar(50) NOT NULL,
  `name` tinytext NOT NULL,
  `url` tinytext,
  `image` varchar(255) DEFAULT NULL COMMENT 'Contiene nombre de archivo',
  `order` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Patrocinadores de convocatorias';

CREATE TABLE `campaign` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `node` varchar(50) NOT NULL,
  `call` varchar(50) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '0',
  `order` smallint(5) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `call_node` (`node`,`call`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Convocatorias en portada';

CREATE TABLE `category` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext,
  `description` text,
  `order` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Categorias de los proyectos';

CREATE TABLE `category_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` tinytext,
  `description` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `comment` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post` bigint(20) unsigned NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `text` text NOT NULL,
  `user` varchar(50) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Comentarios';

CREATE TABLE `conf` (
  `key` varchar(255) NOT NULL COMMENT 'Clave',
  `value` varchar(255) NOT NULL COMMENT 'Valor'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Para guardar pares para configuraciones, bloqueos etc';

CREATE TABLE `contract` (
  `project` varchar(50) NOT NULL,
  `number` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL COMMENT 'dia anterior a la publicacion',
  `enddate` date NOT NULL COMMENT 'finalización, un año despues de la fecha de contrato',
  `pdf` varchar(255) DEFAULT NULL COMMENT 'Archivo pdf contrato',
  `type` varchar(1) NOT NULL DEFAULT '0' COMMENT '0 = persona física; 1 = representante asociacion; 2 = apoderado entidad mercantil',
  `name` tinytext,
  `nif` varchar(10) DEFAULT NULL,
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
  UNIQUE KEY `numero` (`number`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Contratos';

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
  `recieved` int(1) NOT NULL DEFAULT '0' COMMENT 'Se ha recibido el contrato firmado',
  `recieved_date` date DEFAULT NULL COMMENT 'Fecha que se cambia el flag',
  `recieved_user` varchar(50) DEFAULT NULL COMMENT 'Usuario que cambia el flag',
  `payed` int(1) NOT NULL DEFAULT '0' COMMENT 'Se ha realizado el pago al proyecto',
  `payed_date` date DEFAULT NULL COMMENT 'Fecha que se cambia el flag',
  `payed_user` varchar(50) DEFAULT NULL COMMENT 'Usuario que cambia el flag',
  `prepay` int(1) NOT NULL DEFAULT '0' COMMENT 'Ha habido pago avanzado',
  `prepay_date` date DEFAULT NULL COMMENT 'Fecha que se cambia el flag',
  `prepay_user` varchar(50) DEFAULT NULL COMMENT 'Usuario que cambia el flag',
  `closed` int(1) NOT NULL DEFAULT '0' COMMENT 'Contrato finiquitado',
  `closed_date` date DEFAULT NULL COMMENT 'Fecha que se cambia el flag',
  `closed_user` varchar(50) DEFAULT NULL COMMENT 'Usuario que cambia el flag',
  PRIMARY KEY (`contract`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Seguimiento de estado de contrato';

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Desglose de costes de proyectos';

CREATE TABLE `cost_lang` (
  `id` int(20) NOT NULL,
  `project` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `cost` tinytext,
  `description` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`),
  KEY `project` (`project`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `criteria` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `section` varchar(50) NOT NULL DEFAULT 'node',
  `title` tinytext,
  `description` text,
  `order` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Criterios de puntuación';

CREATE TABLE `criteria_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `description` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `document` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contract` varchar(50) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `size` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `faq` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `node` varchar(50) NOT NULL,
  `section` varchar(50) NOT NULL DEFAULT 'node',
  `title` tinytext,
  `description` text,
  `order` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Preguntas frecuentes';

CREATE TABLE `faq_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `description` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Log de eventos';

CREATE TABLE `geologin` (
  `user` varchar(50) NOT NULL DEFAULT '',
  `ip` varchar(15) NOT NULL,
  `lon` decimal(14,12) DEFAULT NULL,
  `lat` decimal(14,12) DEFAULT NULL,
  `msg` tinytext,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Guarda dats de login';

CREATE TABLE `glossary` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` tinytext,
  `text` longtext COMMENT 'texto de la entrada',
  `media` tinytext,
  `legend` text,
  `gallery` varchar(2000) DEFAULT NULL COMMENT 'Galería de imagenes',
  `image` varchar(255) DEFAULT NULL COMMENT 'Imagen principal',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Entradas para el glosario';

CREATE TABLE `glossary_image` (
  `glossary` bigint(20) NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT 'Contiene nombre de archivo',
  PRIMARY KEY (`glossary`,`image`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `glossary_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `text` longtext,
  `legend` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `home` (
  `item` varchar(10) NOT NULL,
  `type` varchar(5) DEFAULT 'main' COMMENT 'lateral o central',
  `node` varchar(50) NOT NULL,
  `order` smallint(5) unsigned NOT NULL DEFAULT '1',
  UNIQUE KEY `item_node` (`item`,`node`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Elementos en portada';

CREATE TABLE `icon` (
  `id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` tinytext,
  `group` varchar(50) DEFAULT NULL COMMENT 'exclusivo para grupo',
  `order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Iconos para retorno/recompensa';

CREATE TABLE `icon_lang` (
  `id` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` tinytext,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `icon_license` (
  `icon` varchar(50) NOT NULL,
  `license` varchar(50) NOT NULL,
  UNIQUE KEY `icon` (`icon`,`license`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Licencias para cada icono, solo social';

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Entradas about';

CREATE TABLE `info_image` (
  `info` bigint(20) NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT 'Contiene nombre de archivo',
  PRIMARY KEY (`info`,`image`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `info_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `text` longtext,
  `legend` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `invest` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(50) NOT NULL,
  `project` varchar(50) NOT NULL,
  `account` varchar(256) NOT NULL COMMENT 'Solo para aportes de cash',
  `amount` int(6) NOT NULL,
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `usuario` (`user`),
  KEY `proyecto` (`project`),
  KEY `convocatoria` (`call`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Aportes monetarios a proyectos';

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
  PRIMARY KEY (`invest`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Dirección de entrega de recompensa';

CREATE TABLE `invest_detail` (
  `invest` bigint(20) NOT NULL,
  `type` varchar(30) NOT NULL,
  `log` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `invest_type` (`invest`,`type`),
  KEY `invest` (`invest`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Detalles de los aportes';

CREATE TABLE `invest_node` (
  `user_id` varchar(50) NOT NULL,
  `user_node` varchar(50) NOT NULL,
  `project_id` varchar(50) NOT NULL,
  `project_node` varchar(50) NOT NULL,
  `invest_id` bigint(20) NOT NULL,
  `invest_node` varchar(50) NOT NULL COMMENT 'Nodo en el que se hace el aporte',
  UNIQUE KEY `invest` (`invest_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Aportes por usuario/nodo a proyecto/nodo';

CREATE TABLE `invest_reward` (
  `invest` bigint(20) unsigned NOT NULL,
  `reward` bigint(20) unsigned NOT NULL,
  `fulfilled` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `invest` (`invest`,`reward`),
  KEY `reward` (`reward`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Recompensas elegidas al aportar';

CREATE TABLE `lang` (
  `id` varchar(2) NOT NULL COMMENT 'Código ISO-639',
  `name` varchar(20) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '0',
  `short` varchar(10) DEFAULT NULL,
  `locale` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Idiomas';

CREATE TABLE `license` (
  `id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` tinytext,
  `group` varchar(50) DEFAULT NULL COMMENT 'grupo de restriccion de menor a mayor',
  `url` varchar(256) DEFAULT NULL,
  `order` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Licencias de distribucion';

CREATE TABLE `license_lang` (
  `id` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` tinytext,
  `url` varchar(256) DEFAULT NULL,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `location` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `location` tinytext,
  `region` tinytext,
  `country` tinytext NOT NULL,
  `lon` decimal(16,14) NOT NULL,
  `lat` decimal(16,14) NOT NULL,
  `valid` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Geolocalización';

CREATE TABLE `location_item` (
  `location` int(20) unsigned NOT NULL,
  `item` varchar(50) NOT NULL,
  `type` varchar(7) NOT NULL,
  KEY `itemtipo` (`item`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tabla de relacion localizaciones y registros';

CREATE TABLE `mail` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` tinytext NOT NULL,
  `html` longtext NOT NULL,
  `template` int(20) DEFAULT NULL,
  `node` varchar(50) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lang` varchar(2) DEFAULT NULL COMMENT 'Idioma en el que se solicitó la plantilla',
  `content` varchar(50) DEFAULT NULL COMMENT 'ID del archivo con HTML estático',
  PRIMARY KEY (`id`),
  KEY `email` (`email`(255))
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Contenido enviado por email para el -si no ves-';

CREATE TABLE `mailer_content` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `active` int(1) NOT NULL DEFAULT '1',
  `mail` int(20) NOT NULL,
  `subject` text NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `blocked` int(1) DEFAULT NULL,
  `reply` varchar(255) DEFAULT NULL COMMENT 'Email remitente',
  `reply_name` text COMMENT 'Nombre remitente',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Contenido a enviar';

CREATE TABLE `mailer_control` (
  `email` char(150) NOT NULL,
  `bounces` int(10) unsigned NOT NULL,
  `complaints` int(10) unsigned NOT NULL,
  `action` enum('allow','deny') DEFAULT 'allow',
  `last_reason` char(255) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Lista negra para bounces y complaints';

CREATE TABLE `mailer_limit` (
  `hora` time NOT NULL COMMENT 'Hora envio',
  `num` int(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Cuantos',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`hora`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Para limitar el número de envios diarios';

CREATE TABLE `mailer_send` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `mailing` int(20) unsigned NOT NULL COMMENT 'Id de mailer_content',
  `user` varchar(50) NOT NULL,
  `email` varchar(256) NOT NULL,
  `name` varchar(100) NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sended` int(1) DEFAULT NULL,
  `error` text,
  `blocked` int(1) DEFAULT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `mailing` (`mailing`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Destinatarios pendientes y realizados';

CREATE TABLE `message` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Mensajes de usuarios en proyecto';

CREATE TABLE `message_lang` (
  `id` int(20) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `message` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Noticias en la cabecera';

CREATE TABLE `news_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `description` text,
  `url` tinytext,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Nodos';

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
  PRIMARY KEY (`node`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Datos resumen nodo';

CREATE TABLE `node_lang` (
  `id` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `subtitle` text,
  `description` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `open_tag` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext,
  `description` text,
  `order` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `post` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Agrupacion de los proyectos';

CREATE TABLE `open_tag_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` tinytext,
  `description` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `page` (
  `id` varchar(50) NOT NULL,
  `name` tinytext NOT NULL,
  `description` text,
  `url` tinytext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Páginas institucionales';

CREATE TABLE `page_lang` (
  `id` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` tinytext NOT NULL,
  `description` text,
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `page_node` (
  `page` varchar(50) NOT NULL,
  `node` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` tinytext,
  `description` text,
  `content` longtext,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `page` (`page`,`node`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Contenidos de las paginas';

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
  UNIQUE KEY `user_project_node` (`node`,`project`,`user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Proyectos recomendados por padrinos';

CREATE TABLE `patron_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `description` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `patron_order` (
  `id` varchar(50) NOT NULL,
  `order` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Orden de los padrinos';

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
  `gallery` varchar(2000) DEFAULT NULL COMMENT 'Galería de imagenes',
  `num_comments` int(10) unsigned DEFAULT NULL COMMENT 'Número de comentarios que recibe el post',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `portada` (`home`),
  KEY `pie` (`footer`),
  KEY `publicadas` (`publish`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Entradas para la portada';

CREATE TABLE `post_image` (
  `post` bigint(20) NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT 'Contiene nombre de archivo',
  PRIMARY KEY (`post`,`image`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `post_lang` (
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

CREATE TABLE `post_node` (
  `post` bigint(20) unsigned NOT NULL,
  `node` varchar(50) NOT NULL,
  `order` int(11) DEFAULT '1',
  PRIMARY KEY (`post`,`node`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Entradas para la portada de nodos';

CREATE TABLE `post_tag` (
  `post` bigint(20) unsigned NOT NULL,
  `tag` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`post`,`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tags de las entradas';

CREATE TABLE `project` (
  `id` varchar(50) NOT NULL,
  `name` tinytext,
  `subtitle` tinytext,
  `lang` varchar(2) DEFAULT 'es',
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
  `called` varchar(50) DEFAULT NULL COMMENT 'Convocatoria en la que está',
  `maxproj` int(5) DEFAULT NULL COMMENT 'Dinero que puede conseguir un proyecto de la convocatoria',
  `gallery` varchar(10000) DEFAULT NULL COMMENT 'Galería de imagenes',
  PRIMARY KEY (`id`),
  KEY `owner` (`owner`),
  KEY `nodo` (`node`),
  KEY `estado` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Proyectos de la plataforma';

CREATE TABLE `project_account` (
  `project` varchar(50) NOT NULL,
  `bank` tinytext,
  `bank_owner` tinytext,
  `paypal` tinytext,
  `paypal_owner` tinytext,
  `allowpp` int(1) DEFAULT NULL,
  PRIMARY KEY (`project`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Cuentas bancarias de proyecto';

CREATE TABLE `project_category` (
  `project` varchar(50) NOT NULL,
  `category` int(12) NOT NULL,
  UNIQUE KEY `project_category` (`project`,`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Categorias de los proyectos';

CREATE TABLE `project_conf` (
  `project` varchar(50) NOT NULL,
  `noinvest` int(1) NOT NULL DEFAULT '0' COMMENT 'No se permiten más aportes',
  `watch` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Vigilar el proyecto',
  `days_round1` int(4) DEFAULT '40' COMMENT 'Días que dura la primera ronda desde la publicación del proyecto',
  `days_round2` int(4) DEFAULT '40' COMMENT 'Días que dura la segunda ronda desde la publicación del proyecto',
  `one_round` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Si el proyecto tiene una unica ronda',
  PRIMARY KEY (`project`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Configuraciones para proyectos';

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

CREATE TABLE `project_image` (
  `project` varchar(50) NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT 'Contiene nombre de archivo',
  `section` varchar(50) DEFAULT NULL,
  `url` tinytext,
  `order` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`project`,`image`),
  KEY `proyecto-seccion` (`project`,`section`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `project_open_tag` (
  `project` varchar(50) NOT NULL,
  `open_tag` int(12) NOT NULL,
  UNIQUE KEY `project_open_tag` (`project`,`open_tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Agrupacion de los proyectos';

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
  KEY `activos` (`active`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Proyectos destacados';

CREATE TABLE `promote_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `description` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `purpose` (
  `text` varchar(50) NOT NULL,
  `purpose` text NOT NULL,
  `html` tinyint(1) DEFAULT NULL COMMENT 'Si el texto lleva formato html',
  `group` varchar(50) NOT NULL DEFAULT 'general' COMMENT 'Agrupacion de uso',
  PRIMARY KEY (`text`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Explicación del propósito de los textos';

CREATE TABLE `review` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `to_checker` text,
  `to_owner` text,
  `score` int(2) NOT NULL DEFAULT '0',
  `max` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Revision para evaluacion de proyecto';

CREATE TABLE `review_comment` (
  `review` bigint(20) unsigned NOT NULL,
  `user` varchar(50) NOT NULL,
  `section` varchar(50) NOT NULL,
  `evaluation` text,
  `recommendation` text,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`review`,`user`,`section`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Comentarios de revision';

CREATE TABLE `review_score` (
  `review` bigint(20) unsigned NOT NULL,
  `user` varchar(50) NOT NULL,
  `criteria` bigint(20) unsigned NOT NULL,
  `score` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`review`,`user`,`criteria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Puntuacion por citerio';

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `project` (`project`),
  KEY `icon` (`icon`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Retornos colectivos e individuales';

CREATE TABLE `reward_lang` (
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

CREATE TABLE `role` (
  `id` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `sponsor` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `url` tinytext,
  `image` varchar(255) DEFAULT NULL COMMENT 'Contiene nombre de archivo',
  `order` int(11) NOT NULL DEFAULT '1',
  `node` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Patrocinadores';

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Historias existosas';

CREATE TABLE `stories_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `description` text,
  `review` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `support` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project` varchar(50) NOT NULL,
  `support` tinytext,
  `description` text,
  `type` varchar(50) DEFAULT NULL,
  `thread` bigint(20) unsigned DEFAULT NULL COMMENT 'De la tabla message',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Colaboraciones';

CREATE TABLE `support_lang` (
  `id` int(20) NOT NULL,
  `project` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `support` tinytext,
  `description` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`),
  KEY `project` (`project`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tag` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Tags de blogs (de nodo)';

CREATE TABLE `tag_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` tinytext,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `task` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `node` varchar(50) NOT NULL,
  `text` text NOT NULL,
  `url` tinytext,
  `done` varchar(50) DEFAULT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tareas pendientes de admin';

CREATE TABLE `template` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `group` varchar(50) NOT NULL DEFAULT 'general' COMMENT 'Agrupación de uso',
  `purpose` tinytext NOT NULL,
  `title` tinytext NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Plantillas emails automáticos';

CREATE TABLE `template_lang` (
  `id` bigint(20) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` tinytext,
  `text` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `text` (
  `id` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL DEFAULT 'es',
  `text` text NOT NULL,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  PRIMARY KEY (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Textos multi-idioma';

CREATE TABLE `unlocable` (
  `user` varchar(50) NOT NULL,
  PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='usuarios con localidad inlocalizable';

CREATE TABLE `user` (
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
  KEY `coordenadas` (`location`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_call` (
  `user` varchar(50) NOT NULL,
  `call` varchar(50) NOT NULL,
  PRIMARY KEY (`user`,`call`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Asignacion de convocatorias a admines';

CREATE TABLE `user_donation` (
  `user` varchar(50) NOT NULL,
  `amount` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `surname` varchar(255) DEFAULT NULL COMMENT 'Apellido',
  `nif` varchar(12) DEFAULT NULL,
  `address` tinytext,
  `zipcode` varchar(10) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `numproj` int(2) DEFAULT '1',
  `year` varchar(4) NOT NULL,
  `edited` int(1) DEFAULT '0' COMMENT 'Revisados por el usuario',
  `confirmed` int(1) DEFAULT '0' COMMENT 'Certificado generado',
  `pdf` varchar(255) DEFAULT NULL COMMENT 'nombre del archivo de certificado',
  PRIMARY KEY (`user`,`year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Datos fiscales donativo';

CREATE TABLE `user_interest` (
  `user` varchar(50) NOT NULL,
  `interest` int(12) NOT NULL,
  UNIQUE KEY `user_interest` (`user`,`interest`),
  KEY `usuario` (`user`),
  KEY `interes` (`interest`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Intereses de los usuarios';

CREATE TABLE `user_lang` (
  `id` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `about` text,
  `keywords` tinytext,
  `contribution` text,
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_login` (
  `user` varchar(50) NOT NULL,
  `provider` varchar(50) NOT NULL,
  `oauth_token` text NOT NULL,
  `oauth_token_secret` text NOT NULL,
  `datetime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user`,`oauth_token`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_node` (
  `user` varchar(50) NOT NULL,
  `node` varchar(50) NOT NULL,
  PRIMARY KEY (`user`,`node`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

CREATE TABLE `user_prefer` (
  `user` varchar(50) NOT NULL,
  `updates` int(1) NOT NULL DEFAULT '0',
  `threads` int(1) NOT NULL DEFAULT '0',
  `rounds` int(1) NOT NULL DEFAULT '0',
  `mailing` int(1) NOT NULL DEFAULT '0',
  `email` int(1) NOT NULL DEFAULT '0',
  `tips` int(1) NOT NULL DEFAULT '0',
  `comlang` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Preferencias de notificacion de usuario';

CREATE TABLE `user_project` (
  `user` varchar(50) NOT NULL,
  `project` varchar(50) NOT NULL,
  UNIQUE KEY `user` (`user`,`project`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_review` (
  `user` varchar(50) NOT NULL,
  `review` bigint(20) unsigned NOT NULL,
  `ready` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Ha terminado con la revision',
  PRIMARY KEY (`user`,`review`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Asignacion de revision a usuario';

CREATE TABLE `user_role` (
  `user_id` varchar(50) NOT NULL,
  `role_id` varchar(50) NOT NULL,
  `node_id` varchar(50) NOT NULL,
  `datetime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `user_FK` (`user_id`),
  KEY `role_FK` (`role_id`),
  KEY `node_FK` (`node_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_translang` (
  `user` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  PRIMARY KEY (`user`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Idiomas de traductores';

CREATE TABLE `user_translate` (
  `user` varchar(50) NOT NULL,
  `type` varchar(10) NOT NULL COMMENT 'Tipo de contenido',
  `item` varchar(50) NOT NULL COMMENT 'id del contenido',
  `ready` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Ha terminado con la traduccion',
  PRIMARY KEY (`user`,`type`,`item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Asignacion de traduccion a usuario';

CREATE TABLE `user_vip` (
  `user` varchar(50) NOT NULL,
  `image` varchar(255) DEFAULT NULL COMMENT 'Contiene nombre de archivo',
  PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Datos usuario colaborador';

CREATE TABLE `user_web` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(50) NOT NULL,
  `url` tinytext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Webs de los usuarios';

CREATE TABLE `worthcracy` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `amount` int(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Niveles de meritocracia';

CREATE TABLE `worthcracy_lang` (
  `id` int(2) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` tinytext NOT NULL,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

