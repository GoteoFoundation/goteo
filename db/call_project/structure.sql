CREATE TABLE IF NOT EXISTS call_project (
  `call` varchar(50) NOT NULL,
  project varchar(50) NOT NULL,
  UNIQUE KEY call_project (`call`,project)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Proyectos asignados a convocatorias';