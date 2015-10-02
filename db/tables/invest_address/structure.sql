CREATE TABLE IF NOT EXISTS `invest_address` (
  `invest` bigint(20) unsigned NOT NULL,
  `user` varchar(50) NOT NULL,
  `address` tinytext,
  `zipcode` varchar(10) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `nif` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`invest`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Dirección de entrega de recompensa';


-- alters
ALTER TABLE `invest_address` ADD `name` VARCHAR( 255 ) NULL ,
ADD `nif` VARCHAR( 10 ) NULL ;

-- alters
ALTER TABLE `invest_address` ADD `namedest` TINYTEXT NULL, ADD `emaildest` TINYTEXT NULL, ADD `regalo` INT(1)  NULL DEFAULT '0';
ALTER TABLE `invest_address` ADD `message` TEXT NULL;
