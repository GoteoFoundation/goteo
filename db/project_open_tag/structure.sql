CREATE TABLE IF NOT EXISTS project_open_tag (
  project varchar(50) NOT NULL,
  open_tag int(12) NOT NULL,
  UNIQUE KEY project_open_tag (project,open_tag)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Agrupacion de los proyectos';