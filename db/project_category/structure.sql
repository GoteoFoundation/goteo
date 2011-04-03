CREATE TABLE IF NOT EXISTS project_category (
  project varchar(50) NOT NULL,
  category int(12) NOT NULL,
  UNIQUE KEY project_category (project,category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Categorias de los proyectos';