CREATE TABLE IF NOT EXISTS project_category (
  project varchar(50) NOT NULL,
  category int(12) NOT NULL,
  UNIQUE KEY project_category (project,category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Categorias de los proyectos';


#claves añadidas para joins

ALTER TABLE `project_category`
    CHANGE `category` `category` BIGINT(20) UNSIGNED NOT NULL AFTER `project` ,
    ADD KEY `category`(`category`) ,
    ADD KEY `project`(`project`) ;

ALTER TABLE `project_category`
    ADD CONSTRAINT `fk_category`
    FOREIGN KEY (`category`) REFERENCES `category` (`id`) ,
    ADD CONSTRAINT `fk_project`
    FOREIGN KEY (`project`) REFERENCES `project` (`id`) ;
