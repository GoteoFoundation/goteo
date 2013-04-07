CREATE TABLE `location_item` (
  `location` int(20) unsigned NOT NULL,
  `item` varchar(50) CHARACTER SET utf8 NOT NULL,
  `type` varchar(7) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabla de relacion localizaciones y registros';
