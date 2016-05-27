CREATE TABLE IF NOT EXISTS call_post (
  `call` varchar(50) NOT NULL,
  post int(20) NOT NULL,
  UNIQUE KEY call_post (`call`,post)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Entradas de blog asignadas a convocatorias';