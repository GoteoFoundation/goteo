CREATE TABLE `user_translate` (
`user` VARCHAR( 50 ) NOT NULL ,
`project` VARCHAR( 50 ) NOT NULL ,
`ready` BOOLEAN NOT NULL DEFAULT '0' COMMENT 'Ha terminado con la traduccion',
PRIMARY KEY ( `user` , `project` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Asignacion de traduccion a usuario';
