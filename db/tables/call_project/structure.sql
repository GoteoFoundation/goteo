CREATE TABLE IF NOT EXISTS call_project (
  `call` varchar(50) NOT NULL,
  project varchar(50) NOT NULL,
  UNIQUE KEY call_project (`call`,project)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Proyectos asignados a convocatorias';


ALTER TABLE `call_project` ADD FOREIGN KEY (`call`) REFERENCES `call`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
                           ADD FOREIGN KEY (`project`) REFERENCES `project`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

