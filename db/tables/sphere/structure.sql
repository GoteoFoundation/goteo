-- Ámbitos de influencia de una convocatoria

CREATE TABLE `sphere` (
`id` SERIAL NOT NULL ,
`name` VARCHAR( 255 ),
`image` VARCHAR( 255 ) NULL DEFAULT NULL,
PRIMARY KEY ( `id` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Ámbitos de convocatorias';