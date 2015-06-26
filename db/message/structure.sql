CREATE TABLE `message` (
`id` SERIAL NOT NULL ,
`user` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`project` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`thread` BIGINT UNSIGNED NULL ,
`date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
`message` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`blocked` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'No se puede modificar ni borrar',
`closed` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'No se puede responder',
PRIMARY KEY ( `id` )
) ENGINE = InnoDB COMMENT = 'Mensajes de usuarios en proyecto';


-- Alter table por si no se puede recrear la tabla

-- Para marcar si un mensaje no se puede editar ni borrar
ALTER TABLE `message` ADD `blocked` BOOLEAN NOT NULL DEFAULT '0' COMMENT 'No se puede modificar ni borrar';

-- Para marcar si un mensaje (que sea el hilo) no se puede responder
ALTER TABLE `message` ADD `closed` BOOLEAN NOT NULL DEFAULT '0' COMMENT 'No se puede responder';
