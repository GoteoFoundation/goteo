CREATE TABLE IF NOT EXISTS `user_donation` (
  `user` varchar(50) NOT NULL,
  `amount` INT(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `nif` varchar(10) DEFAULT NULL,
  `address` tinytext,
  `zipcode` varchar(10) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `numproj` INT(2) DEFAULT 1,
  `year` VARCHAR(4) NOT NULL,
  PRIMARY KEY (`user`, `year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Datos fiscales donativo';


-- Campos de control
ALTER TABLE `user_donation`
	ADD `edited` INT( 1 ) NULL DEFAULT '0' COMMENT 'Revisados por el usuario',
	ADD `confirmed` INT( 1 ) NULL DEFAULT '0' COMMENT 'Certificado generado';

-- el archivo del certificado asociado
ALTER TABLE `user_donation` ADD `pdf` VARCHAR( 255 ) NULL COMMENT 'nombre del archivo de certificado';

-- Apellido
ALTER TABLE `user_donation` ADD `surname` VARCHAR( 255 ) NULL COMMENT 'Apellido' AFTER `name` ;

-- provincia
ALTER TABLE `user_donation` ADD `region` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'Provincia' AFTER `location` ;

-- nombre pais
ALTER TABLE `user_donation` ADD `countryname` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'Nombre del pais' AFTER `country` ;

-- constrains
ALTER TABLE `user_donation` ADD FOREIGN KEY (`user`) REFERENCES `user`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT;
