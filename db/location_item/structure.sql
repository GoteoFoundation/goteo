CREATE TABLE `location_item` (
  `location` int(20) unsigned NOT NULL,
  `item` varchar(50) CHARACTER SET utf8 NOT NULL,
  `type` varchar(7) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Tabla de relacion localizaciones y registros';

-- Charset
ALTER TABLE `location_item` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
