CREATE TABLE `contract_status` (
`contract`  VARCHAR( 50 ) NOT NULL COMMENT 'Id del proyecto',
`owner` int(1) NOT NULL DEFAULT '0' COMMENT 'El impulsor ha dado por rellenados los datos',
`admin` int(1) NOT NULL DEFAULT '0' COMMENT 'El admin ha comenzado a revisar los datos',
`ready` int(1) NOT NULL DEFAULT '0' COMMENT 'Datos verificados y correctos',
`recieved` int(1) NOT NULL DEFAULT '0' COMMENT 'Se ha recibido el contrato firmado',
`payed` int(1) NOT NULL DEFAULT '0' COMMENT 'Se ha realizado el pago al proyecto',
`pdf` varchar(255) DEFAULT NULL,
PRIMARY KEY ( `contract` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Seguimiento de estado de contrato';

-- otros 2 flags
ALTER TABLE `contract_status`  ADD `prepay` INT(1) NOT NULL COMMENT 'Ha habido pago avanzado',  ADD `closed` INT(1) NOT NULL COMMENT 'Contrato finiquitado';