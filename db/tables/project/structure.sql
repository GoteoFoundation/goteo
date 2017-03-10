CREATE TABLE IF NOT EXISTS `project` (
  `id` varchar(50) NOT NULL,
  `name` tinytext DEFAULT NULL,
  `status` int(1) NOT NULL,
  `progress` int(3) NOT NULL,
  `owner` varchar(50) NOT NULL COMMENT 'usuario que lo ha creado',
  `node` varchar(50) NOT NULL COMMENT 'nodo en el que se ha creado',
  `amount` int(6) DEFAULT NULL COMMENT 'acumulado actualmente',
  `days` int(3) NOT NULL DEFAULT '0' COMMENT 'Dias restantes',
  `created` date DEFAULT NULL,
  `updated` date DEFAULT NULL,
  `published` date DEFAULT NULL,
  `success` date DEFAULT NULL,
  `closed` date DEFAULT NULL,
  `contract_name` varchar(255) DEFAULT NULL,
  `contract_nif` varchar(10) DEFAULT NULL COMMENT 'Guardar sin espacios ni puntos ni guiones',
  `phone` varchar(9) DEFAULT NULL COMMENT 'guardar sin espacios ni puntos',
  `address` tinytext,
  `zipcode` varchar(10) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `image` varchar(256) DEFAULT NULL,
  `description` text,
  `motivation` text,
  `about` text,
  `goal` text,
  `related` text,
  `category` varchar(50) DEFAULT NULL,
  `keywords` tinytext COMMENT 'Separadas por comas',
  `media` varchar(256) DEFAULT NULL,
  `currently` int(1) DEFAULT NULL,
  `project_location` varchar(256) DEFAULT NULL,
  `scope` int(1) DEFAULT NULL,
  `resource` text,
  `comment` text COMMENT 'Comentario para los admin',
  PRIMARY KEY (`id`),
  KEY `owner` (`owner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Proyectos de la plataforma';


-- Los alter por si no se puede recrear la tabla
ALTER TABLE `project` ADD `image` VARCHAR( 256 ) NULL ,
ADD `description` TEXT NULL ,
ADD `motivation` TEXT NULL ,
ADD `about` TEXT NULL ,
ADD `goal` TEXT NULL ,
ADD `related` TEXT NULL ,
ADD `category` VARCHAR( 50 ) NULL ,
ADD `media` VARCHAR( 256 ) NULL ,
ADD `currently` INT( 1 ) NULL ,
ADD `project_location` VARCHAR( 256 ) NULL ,
ADD `resource` TEXT NULL ;

ALTER TABLE `project` ADD `updated` DATE NULL AFTER `created` ;
ALTER TABLE `project` ADD `keywords` TINYTEXT NULL COMMENT 'Separadas por comas' AFTER `category` ;
ALTER TABLE `project` ADD `comment` TEXT NULL COMMENT 'Comentario para los admin';
ALTER TABLE `project` ADD `days` INT( 3 ) NOT NULL DEFAULT '0' COMMENT 'Dias restantes' AFTER `amount` ;
ALTER TABLE `project` CHANGE `name` `name` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL ;

ALTER TABLE `project`
  DROP `contract_surname`,
  DROP `contract_email`;

ALTER TABLE `project` ADD `scope` INT( 1 ) NULL COMMENT 'Ambito de alcance' AFTER `project_location` ;

-- el telefono va talcual
ALTER TABLE `project` CHANGE `phone` `phone` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'guardar talcual'

-- Alters para datos personales para entidad
ALTER TABLE `project` ADD `contract_email` VARCHAR( 255 ) NULL AFTER `phone` ;
ALTER TABLE `project` ADD `contract_entity` int(1) NOT NULL DEFAULT '0';
ALTER TABLE `project` ADD `contract_birthdate` date DEFAULT NULL;
ALTER TABLE `project` ADD `entity_office` varchar(255) DEFAULT NULL COMMENT 'Cargo del responsable';
ALTER TABLE `project` ADD `entity_name` varchar(255) DEFAULT NULL;
ALTER TABLE `project` ADD `entity_cif` varchar(10) DEFAULT NULL COMMENT 'Guardar sin espacios ni puntos ni guiones';
ALTER TABLE `project` ADD `post_address` tinytext DEFAULT NULL;
ALTER TABLE `project` ADD `post_zipcode` varchar(10) DEFAULT NULL;
ALTER TABLE `project` ADD `post_location` varchar(255) DEFAULT NULL;
ALTER TABLE `project` ADD `post_country` varchar(50) DEFAULT NULL;

-- para guardar si es diferente o no
ALTER TABLE `project` ADD `secondary_address` INT NOT NULL DEFAULT '0' AFTER `post_address` ;

-- para marcar el paso a segunda ronda
ALTER TABLE `project` ADD `passed` DATE NULL AFTER `closed`;

-- Idioma original del proyecto
ALTER TABLE `project` ADD `lang` VARCHAR( 2 ) NOT NULL DEFAULT 'es' AFTER `name` ;
-- Subtitulo
ALTER TABLE `project` ADD `subtitle` tinytext DEFAULT NULL AFTER `name` ;

-- Traduccion habilitada
ALTER TABLE `project` ADD `translate` int(1) NOT NULL DEFAULT '0' AFTER `status` ;

-- Video bajo motivación
ALTER TABLE `project` ADD `video` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `motivation` ;

-- Para universal subtitles
ALTER TABLE `project` ADD `video_usubs` INT( 1 ) NOT NULL DEFAULT '0' AFTER `video`;
ALTER TABLE `project` ADD `media_usubs` INT( 1 ) NOT NULL DEFAULT '0' AFTER `media`;

-- Para contener VATs
ALTER TABLE `project` CHANGE `contract_nif` `contract_nif` VARCHAR( 15 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Guardar sin espacios ni puntos ni guiones';

-- Nueva sección de contenido, recompensas
ALTER TABLE `project` ADD `reward` TEXT NULL AFTER `related` ;

-- Añadido mincost y maxcost como campos (para no tener que calcular en cada select)
ALTER TABLE `project` ADD COLUMN `mincost` INT(5) DEFAULT 0 NULL COMMENT 'minimo coste' AFTER `amount`, ADD COLUMN `maxcost` INT(5) DEFAULT 0 NULL COMMENT 'optimo' AFTER `mincost`;

-- Añadido numero de inversores
ALTER TABLE `project` ADD COLUMN `num_investors` INT UNSIGNED NULL DEFAULT NULL COMMENT 'Numero inversores' AFTER `days`;

-- Grado de popularidad (suma num_investors y num_messegers)
ALTER TABLE `project` ADD COLUMN `popularity` INT UNSIGNED NULL DEFAULT NULL COMMENT 'Popularidad del proyecto' AFTER `num_investors`;

-- Añadido numero de usuarios que escriben mensajes
ALTER TABLE `project` ADD COLUMN `num_messengers` INT UNSIGNED NULL DEFAULT NULL COMMENT 'Número de personas que envían mensajes' AFTER `popularity`;

-- Añadido numero de posts
ALTER TABLE `project` ADD COLUMN `num_posts` INT UNSIGNED NULL DEFAULT NULL COMMENT 'Número de post' AFTER `num_messengers`;

-- Recaudación que proviene de los usuarios
ALTER TABLE `project` ADD COLUMN `amount_users` INT UNSIGNED NULL DEFAULT NULL COMMENT 'Recaudación proveniente de los usuarios';

-- Recaudación proveniente de la convocatoria
ALTER TABLE `project` ADD COLUMN `amount_call` INT UNSIGNED NULL DEFAULT NULL COMMENT 'Recaudación proveniente de la convocatoria';

-- Convocatoria en la que está
ALTER TABLE `project` ADD COLUMN `called` VARCHAR(50) COMMENT 'Convocatoria en la que está';

-- Máximo dinero que puede conseguir un proyecto de la convocatoria
ALTER TABLE `project` ADD COLUMN `maxproj` INT(5) COMMENT 'Dinero que puede conseguir un proyecto de la convocatoria';


-- Proyecto

-- Campo calculado para imágenes de la galería  (mayor porque tiene secciones)
ALTER TABLE `project` ADD `gallery` VARCHAR( 10000 ) NULL COMMENT 'Galería de imagenes';

-- Campos calculado para permitir null
ALTER TABLE `project` CHANGE `amount_users` `amount_users` INT( 10 ) UNSIGNED NULL DEFAULT NULL COMMENT 'Recaudación proveniente de los usuarios',
CHANGE `amount_call` `amount_call` INT( 10 ) UNSIGNED NULL DEFAULT NULL COMMENT 'Recaudación proveniente de la convocatoria';


ALTER TABLE `project` CHANGE `num_investors` `num_investors` INT( 10 ) UNSIGNED NULL DEFAULT NULL COMMENT 'Numero inversores',
CHANGE `popularity` `popularity` INT( 10 ) UNSIGNED NULL DEFAULT NULL COMMENT 'Popularidad del proyecto',
CHANGE `num_messengers` `num_messengers` INT( 10 ) UNSIGNED NULL DEFAULT NULL COMMENT 'Número de personas que envían mensajes',
CHANGE `num_posts` `num_posts` INT( 10 ) UNSIGNED NULL DEFAULT NULL COMMENT 'Número de post';

ALTER TABLE `project` CHANGE `mincost` `mincost` INT( 5 ) NULL DEFAULT NULL COMMENT 'minimo coste',
CHANGE `maxcost` `maxcost` INT( 5 ) NULL DEFAULT NULL COMMENT 'optimo';

-- ajuste
UPDATE `project` SET mincost = null, maxcost = null WHERE mincost = 0;
UPDATE `project` SET num_investors = null WHERE num_investors = 0;
UPDATE `project` SET popularity = null WHERE popularity = 0;
UPDATE `project` SET num_messengers = null WHERE num_messengers = 0;
UPDATE `project` SET num_posts = null WHERE num_posts = 0;
UPDATE `project` SET amount_users = null WHERE amount_users = 0;
UPDATE `project` SET amount_call = null WHERE amount_call = 0;

-- divisa del proyecto y ratio original
ALTER TABLE `project` ADD `currency` VARCHAR(4) NOT NULL DEFAULT 'EUR' COMMENT 'Divisa del proyecto' AFTER `lang`;
ALTER TABLE `project` ADD `currency_rate` DECIMAL(9, 5) NOT NULL DEFAULT 1 COMMENT 'Ratio al crear el proyecto' AFTER `currency`;

-- obsolete
ALTER TABLE `project` DROP COLUMN `called`;

-- constrains
ALTER TABLE `project` ADD FOREIGN KEY (`node`) REFERENCES `node`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
                      ADD FOREIGN KEY (`owner`) REFERENCES `user`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT;

--id of Google Analytics for the project
ALTER TABLE `project` ADD `analytics_id` VARCHAR(30);

--facebook pixel
ALTER TABLE `project` ADD `facebook_pixel` VARCHAR(20);

-- social commitment
ALTER TABLE `project` ADD `social_commitment` VARCHAR(50) NULL COMMENT 'Social commitment of the project';

-- social commitment description
ALTER TABLE `project` ADD `social_commitment_description` TEXT COMMENT 'Social commitment of the project';

-- execution plan
ALTER TABLE `project` ADD `execution_plan` TEXT;
ALTER TABLE `project` ADD `execution_plan_url` tinytext DEFAULT NULL;


-- File with sustainability model
ALTER TABLE `project` ADD `sustainability_model` TEXT;
ALTER TABLE `project` ADD `sustainability_model_url` tinytext DEFAULT NULL;

