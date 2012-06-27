CREATE TABLE `project_data` (
`project` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
`invested` INT( 6 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Mostrado en termometro al cerrar',
`fee` INT( 6 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'comisiones cobradas por bancos y paypal a goteo',
`issue` INT( 6 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'importe de las incidencias',
`amount` INT( 6 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'recaudaro realmente',
`goteo` INT( 6 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'comision goteo',
`percent` INT( 1 ) UNSIGNED NOT NULL DEFAULT '8' COMMENT 'porcentaje comision goteo',
`comment` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'comentarios y/o listado de incidencias',
PRIMARY KEY ( `project` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'datos de informe financiero';