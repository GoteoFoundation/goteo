CREATE TABLE IF NOT EXISTS reward (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  project varchar(50) NOT NULL,
  reward varchar(256) NOT NULL,
  description tinytext,
  `type` varchar(50) NOT NULL,
  icon varchar(50) DEFAULT NULL,
  license varchar(50) DEFAULT NULL,
  amount int(5) DEFAULT NULL,
  units int(5) DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY id (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Retornos colectivos e individuales';

-- Alteraciones de la tabla original por si no se puede pasar el create de arriba
ALTER TABLE `reward` ADD `description` TINYTEXT NULL AFTER `reward` ;
ALTER TABLE `reward` CHANGE `reward` `reward` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

-- Cambiando ids numéricos por SERIAL
ALTER TABLE `reward` CHANGE `id` `id` SERIAL NOT NULL AUTO_INCREMENT;