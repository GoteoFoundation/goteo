CREATE TABLE IF NOT EXISTS `call` (
  `id` varchar(50) NOT NULL,
  `name` tinytext DEFAULT NULL,
  `status` int(1) NOT NULL,
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
  `address` tinytext,
  `zipcode` varchar(10) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `logo` varchar(256) DEFAULT NULL,
  `image` varchar(256) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `whom` text DEFAULT NULL,
  `apply` text DEFAULT NULL,
  `legal` longtext DEFAULT NULL,
  `call_location` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `owner` (`owner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Convocatorias';

-- Alters para datos personales para entidad
ALTER TABLE `call` ADD `contract_email` VARCHAR( 255 ) NULL AFTER `phone` ;
ALTER TABLE `call` ADD `contract_entity` int(1) NOT NULL DEFAULT '0';
ALTER TABLE `call` ADD `contract_birthdate` date DEFAULT NULL;
ALTER TABLE `call` ADD `entity_office` varchar(255) DEFAULT NULL COMMENT 'Cargo del responsable';
ALTER TABLE `call` ADD `entity_name` varchar(255) DEFAULT NULL;
ALTER TABLE `call` ADD `entity_cif` varchar(10) DEFAULT NULL COMMENT 'Guardar sin espacios ni puntos ni guiones';
ALTER TABLE `call` ADD `post_address` tinytext DEFAULT NULL;
ALTER TABLE `call` ADD `post_zipcode` varchar(10) DEFAULT NULL;
ALTER TABLE `call` ADD `post_location` varchar(255) DEFAULT NULL;
ALTER TABLE `call` ADD `post_country` varchar(50) DEFAULT NULL;

-- para guardar si es diferente o no
ALTER TABLE `call` ADD `secondary_address` INT NOT NULL DEFAULT '0' AFTER `post_address` ;

-- Idioma original del proyecto
ALTER TABLE `call` ADD `lang` VARCHAR( 2 ) NOT NULL DEFAULT 'es' AFTER `name` ;
-- Subtitulo
ALTER TABLE `call` ADD `subtitle` tinytext DEFAULT NULL AFTER `name` ;

-- Traduccion habilitada
ALTER TABLE `call` ADD `translate` int(1) NOT NULL DEFAULT '0' AFTER `status` ;

-- Dosier informativo
ALTER TABLE `call` ADD `dossier` tinytext DEFAULT NULL AFTER `legal` ;

-- Dias de postulacion
ALTER TABLE `call` ADD `days` int(2) DEFAULT NULL;

-- Ambito
ALTER TABLE `call` ADD `scope` INT(1) DEFAULT NULL AFTER `call_location` ;

-- Plazo inscripcion OBSOLETO (la fecha limite se calcula segun los dias)
ALTER TABLE `call` ADD `until` DATE DEFAULT NULL AFTER `opened` ;
ALTER TABLE `call` DROP `until` ;

-- recursos de capital riego
ALTER TABLE `call` ADD `resources` TEXT NULL DEFAULT NULL COMMENT 'Recursos de capital riego' AFTER `call_location` ;

-- Riego maximo por aporte
ALTER TABLE `call` ADD `maxdrop` int(6) DEFAULT NULL COMMENT 'Riego maximo por aporte';

-- Otra imagen de fondo para las paginas internas
ALTER TABLE `call` ADD `backimage` VARCHAR( 255 ) NULL AFTER `image` ;

-- Riego maximo por proyecto
ALTER TABLE `call` ADD `maxproj` int(6) DEFAULT NULL COMMENT 'Riego maximo por proyecto';

-- Texto para el tweet
ALTER TABLE `call` ADD `tweet` tinytext DEFAULT NULL AFTER `dossier` ;

-- App facebook
ALTER TABLE `call` ADD `fbappid` tinytext DEFAULT NULL AFTER `tweet` ;

-- Modalidad del maximo por proyecto
ALTER TABLE `call` ADD `modemaxp` VARCHAR(3) DEFAULT 'imp' COMMENT 'Modalidad del máximo por proyecto: imp = importe, per = porcentaje' AFTER `maxdrop`;


-- Añadido numero de usuarios proyectos publicados
ALTER TABLE `call` ADD COLUMN `num_projects` INT UNSIGNED DEFAULT 0 NOT NULL COMMENT 'Número de proyectos publicados';

-- Añadido numero de usuarios proyectos publicados
ALTER TABLE `call` ADD COLUMN `num_projects` INT UNSIGNED DEFAULT 0 NOT NULL COMMENT 'Número de proyectos publicados';

-- Recaudación que proviene de los usuarios
ALTER TABLE `call` ADD COLUMN `amount_users` INT UNSIGNED DEFAULT 0 NOT NULL COMMENT 'Recaudación proveniente de los usuarios';

-- Recaudación proveniente de la convocatoria
ALTER TABLE `call` ADD COLUMN `amount_call` INT UNSIGNED DEFAULT 0 NOT NULL COMMENT 'Recaudación proveniente de la convocatoria';
