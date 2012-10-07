CREATE TABLE `contract_status` (
`contract`  bigint(20) unsigned NOT NULL,
`owner` int(1) NOT NULL DEFAULT '0' COMMENT 'El impulsor ha dado por rellenados los datos',
`admin` int(1) NOT NULL DEFAULT '0' COMMENT 'El admin ha dado por válidos los datos',
`pdf` varchar(255) DEFAULT NULL,
PRIMARY KEY ( `contract` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Seguimiento de estado de contrato';
