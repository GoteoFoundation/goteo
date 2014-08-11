CREATE TABLE `project_conf` (
`project` VARCHAR( 50 ) NOT NULL ,
`noinvest` INT( 1 ) NOT NULL DEFAULT '0' COMMENT 'No se permiten más aportes',
PRIMARY KEY ( `project` )
) ENGINE = InnoDB COMMENT = 'Configuraciones para proyectos';

ALTER TABLE `project_conf` ADD `watch` BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Vigilar el proyecto';
ALTER TABLE `project_conf` ADD `days_round1` INT(4) DEFAULT 40 COMMENT 'Días que dura la primera ronda';
ALTER TABLE `project_conf` ADD `days_round2` INT(4) DEFAULT 40 COMMENT 'Días que dura la segunda ronda';
ALTER TABLE `project_conf` ADD `one_round` BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Si el proyecto tiene una unica ronda';
