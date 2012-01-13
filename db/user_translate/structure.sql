CREATE TABLE `user_translate` (
`user` VARCHAR( 50 ) NOT NULL ,
`project` VARCHAR( 50 ) NOT NULL ,
`ready` BOOLEAN NOT NULL DEFAULT '0' COMMENT 'Ha terminado con la traduccion',
PRIMARY KEY ( `user` , `project` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Asignacion de traduccion a usuario';

-- Para asignar multiples contenidos (proyectos, convvocatorias)
ALTER TABLE `user_translate` ADD `type` VARCHAR( 10 ) NOT NULL COMMENT 'Tipo de contenido' AFTER `project` ,
ADD `item` VARCHAR( 50 ) NOT NULL COMMENT 'id del contenido' AFTER `type` ;

-- Cambio de indice
ALTER TABLE `user_translate` DROP PRIMARY KEY ,
ADD PRIMARY KEY ( `user` , `type` , `item` );

-- Eliminar campo obsoleto
ALTER TABLE `user_translate` DROP `project` ;