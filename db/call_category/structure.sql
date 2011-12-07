CREATE TABLE IF NOT EXISTS call_category (
  `call` varchar(50) NOT NULL,
  category int(12) NOT NULL,
  UNIQUE KEY call_category (`call`,category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Categorias de las convocatorias';