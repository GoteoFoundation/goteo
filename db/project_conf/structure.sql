CREATE TABLE `project_conf` (
`project` VARCHAR( 50 ) NOT NULL ,
`noinvest` INT( 1 ) NOT NULL DEFAULT '0' COMMENT 'No se permiten más aportes',
PRIMARY KEY ( `project` )
) ENGINE = InnoDB COMMENT = 'Configuraciones para proyectos';