CREATE TABLE IF NOT EXISTS reward (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  project varchar(50) NOT NULL,
  reward varchar(256),
  description tinytext,
  `type` varchar(50) DEFAULT NULL,
  icon varchar(50) DEFAULT NULL,
  license varchar(50) DEFAULT NULL,
  amount int(5) DEFAULT NULL,
  units int(5) DEFAULT NULL,
  `fulsocial` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Retorno colectivo cumplido',
  PRIMARY KEY (id),
  UNIQUE KEY id (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Retornos colectivos e individuales';



-- Alteraciones de la tabla original por si no se puede pasar el create de arriba
ALTER TABLE `reward` ADD `description` TINYTEXT NULL AFTER `reward` ;
ALTER TABLE `reward` CHANGE `reward` `reward` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

-- Cambiando ids numéricos por SERIAL
ALTER TABLE `reward` CHANGE `id` `id` SERIAL NOT NULL AUTO_INCREMENT;

-- Para marcar retornos colectivos como cumplidos
ALTER TABLE `reward` ADD `fulsocial` BOOLEAN NOT NULL DEFAULT '0' COMMENT 'Retorno colectivo cumplido';

-- PAra especificar el tipo de retorno si eligen otro
ALTER TABLE `reward` ADD `other` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'Otro tipo de recompensa' AFTER `icon`;

-- tamaño de los campos
ALTER TABLE `reward` CHANGE `reward` `reward` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `reward` CHANGE `description` `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

-- url localización del retorno
ALTER TABLE `reward` ADD `url` TINYTEXT NULL DEFAULT '' COMMENT 'Localización del Retorno cumplido';

-- añadido indice para proyecto
ALTER TABLE `reward` ADD INDEX `project` (`project`);
-- orden
ALTER TABLE `reward` ADD `order` TINYINT NOT NULL DEFAULT '1' COMMENT 'Orden para retornos colectivos';

-- Para marcar retornos colectivos adicionales
ALTER TABLE `reward` ADD `bonus` BOOLEAN NOT NULL DEFAULT '0' COMMENT 'Retorno colectivo adicional';


-- optimization
ALTER TABLE `reward` ADD INDEX `icon` ( `icon` );
ALTER TABLE `reward` ADD KEY `type`(`type`) ;
ALTER TABLE `reward` DROP INDEX `id`, ADD INDEX (`order`);
