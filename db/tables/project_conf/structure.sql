CREATE TABLE `project_conf` (
`project` VARCHAR( 50 ) NOT NULL ,
`noinvest` INT( 1 ) NOT NULL DEFAULT '0' COMMENT 'No se permiten más aportes',
PRIMARY KEY ( `project` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Configuraciones para proyectos';

ALTER TABLE `project_conf` ADD `watch` BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Vigilar el proyecto';
ALTER TABLE `project_conf` ADD `days_round1` INT(4) DEFAULT 40 COMMENT 'Días que dura la primera ronda';
ALTER TABLE `project_conf` ADD `days_round2` INT(4) DEFAULT 40 COMMENT 'Días que dura la segunda ronda';
ALTER TABLE `project_conf` ADD `one_round` BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Si el proyecto tiene una unica ronda';

--Indica si ha solicitado ayuda a través del botón ni idea en el formulario
ALTER TABLE `project_conf` ADD `help_license` BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Si necesita ayuda en licencias';
ALTER TABLE `project_conf` ADD `help_cost` BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Si necesita ayuda en costes';

ALTER TABLE `project_conf` ADD `mincost_estimation` INT(11);
ALTER TABLE `project_conf` ADD `publishing_estimation` DATE DEFAULT NULL;

-- Charset
ALTER TABLE `project_conf` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `project_conf` CHANGE `project` `project` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

-- Foreign index
DELETE FROM `project_conf` WHERE `project` NOT IN (SELECT `id` FROM `project`);

ALTER TABLE `project_conf`
    ADD CONSTRAINT `project_conf_ibfk_1`
    FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;
