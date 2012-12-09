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