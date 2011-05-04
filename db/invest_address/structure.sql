CREATE TABLE IF NOT EXISTS `invest_address` (
  `invest` bigint(20) unsigned NOT NULL,
  `user` varchar(50) NOT NULL,
  `address` tinytext,
  `zipcode` varchar(10) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`invest`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Dirección de entrega de recompensa';
