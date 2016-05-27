CREATE TABLE IF NOT EXISTS call_icon (
  `call` varchar(50) NOT NULL,
  icon varchar(50) NOT NULL,
  UNIQUE KEY call_icon (`call`,icon)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tipos de retorno de las convocatorias';