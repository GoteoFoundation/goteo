CREATE TABLE IF NOT EXISTS `contract` (
  `id` SERIAL NOT NULL AUTO_INCREMENT ,
  `project` varchar(50) NOT NULL,
  `number` varchar(20) NOT NULL,
  `type` varchar(1) NOT NULL DEFAULT '0' COMMENT '0 = persona física; 1 = representante asociacion; 2 = apoderado entidad mercantil',
  `name` tinytext DEFAULT NULL,
  `surname` tinytext DEFAULT NULL,
  `nif` varchar(10) DEFAULT NULL,
  `office` tinytext DEFAULT NULL COMMENT 'Cargo en la asociación o empresa',
  `address` tinytext DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,

  `entity_name` tinytext DEFAULT NULL,
  `entity_cif` varchar(10) DEFAULT NULL,
  `entity_address` tinytext DEFAULT NULL,
  `entity_location` varchar(255) DEFAULT NULL,
  `entity_region` varchar(255) DEFAULT NULL,
  `entity_country` varchar(50) DEFAULT NULL,

  `reg_name` tinytext DEFAULT NULL COMMENT 'Nombre del registro en el que esta inscrita la entidad',
  `reg_number` tinytext DEFAULT NULL COMMENT 'Número de registro',
  `reg_id` tinytext DEFAULT NULL COMMENT 'Número de protocolo del notario',

  `project_name` tinytext DEFAULT NULL COMMENT 'Nombre del proyecto',
  `project_url` varchar(255) DEFAULT NULL COMMENT 'URL del proyecto',
  `project_owner` tinytext DEFAULT NULL COMMENT 'Nombre del impulsor',
  `project_user` tinytext DEFAULT NULL COMMENT 'Nombre del usuario autor del proyecto',
  `project_profile` varchar(255) DEFAULT NULL COMMENT 'URL del perfil del autor del proyecto',

  `project_description` TEXT DEFAULT NULL COMMENT 'Breve descripción del proyecto',

  PRIMARY KEY (`id`),
  KEY `project` (`project`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Contratos';

-- campos de cuentas
ALTER TABLE `contract` ADD `bank` TINYTEXT DEFAULT NULL;
ALTER TABLE `contract` ADD `bank_owner` TINYTEXT DEFAULT NULL;
ALTER TABLE `contract` ADD `paypal` TINYTEXT DEFAULT NULL;
ALTER TABLE `contract` ADD `paypal_owner` TINYTEXT DEFAULT NULL;

-- separacion de numero y fecha
ALTER TABLE `contract` CHANGE `number` `number` INT( 11 ) NULL ;
ALTER TABLE `contract` ADD `date` VARCHAR( 12 ) NULL COMMENT 'dia anterior a la publicacion' AFTER `number` ;

-- codigos postales
ALTER TABLE `contract` ADD `zipcode` VARCHAR( 8 ) NULL AFTER `region` ;
ALTER TABLE `contract` ADD `entity_zipcode` VARCHAR( 8 ) NULL AFTER `entity_region` ;

-- fecha de nacimiento
ALTER TABLE `contract` ADD `birthdate` date DEFAULT NULL;

-- del proyecto (objetivos y retornos)
ALTER TABLE `contract` ADD `project_invest` TEXT DEFAULT NULL COMMENT 'objetivo del crowdfunding' AFTER `project_description` ;
ALTER TABLE `contract` ADD `project_return` TEXT DEFAULT NULL COMMENT 'retornos' AFTER `project_invest` ;

-- fecha de escritura
ALTER TABLE `contract` ADD `reg_date` DATE DEFAULT NULL AFTER `reg_name` ;

-- el indice primario es el id del contrato
-- antes de eso hay que eliminar duplicados
ALTER TABLE `contract` DROP PRIMARY KEY, ADD PRIMARY KEY(`project`);
ALTER TABLE `contract` DROP `id`;

-- el numero es autoincrement
ALTER TABLE `contract` ADD UNIQUE `numero` ( `number` ) ;
ALTER TABLE `contract` CHANGE `number` `number` INT( 11 ) NOT NULL AUTO_INCREMENT ;

-- archivo pdf
ALTER TABLE `contract` ADD `pdf` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Archivo pdf contrato' AFTER `date` ;

-- campo fecha de contrato necesitamos que sea Date
ALTER TABLE `contract` CHANGE `date` `date` DATE NOT NULL COMMENT 'dia anterior a la publicacion';
-- y otro campo para fecha final de contrato
ALTER TABLE `contract` ADD `enddate` DATE NOT NULL COMMENT 'finalización, un año despues de la fecha de contrato' AFTER `date` ;

-- campo separado para la ciudad de registro mercantil
-- ALTER TABLE `contract` ADD `reg_loc` tinytext DEFAULT NULL COMMENT 'Ciudad de registro mercantil' AFTER `reg_number` ;
-- pero lovamos a quitar

-- campo para la ciudad de actuación del notario
ALTER TABLE `contract` ADD `reg_idloc` tinytext DEFAULT NULL COMMENT 'Ciudad de actuación del notario' AFTER `reg_id` ;

-- campo para el nombre del notario
ALTER TABLE `contract` ADD `reg_idname` tinytext DEFAULT NULL COMMENT 'Nombre del notario' AFTER `reg_id` ;

-- constrains
ALTER TABLE `contract` ADD FOREIGN KEY (`project`) REFERENCES `project`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT;
