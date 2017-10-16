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

-- pasamos el campo pdf al registro de contrato, no es un flag
ALTER TABLE `contract_status` DROP `pdf`;

-- flag para descarga
ALTER TABLE `contract_status`  ADD `pdf` INT(1) NOT NULL COMMENT 'El impulsor ha descargado el pdf' AFTER `ready`;

-- campos de fecha y user para registrar quien y cuando se canmbia un flag
-- owner
ALTER TABLE `contract_status` ADD `owner_date` DATE NULL DEFAULT NULL COMMENT 'Fecha que se cambia el flag' AFTER `owner`;
ALTER TABLE `contract_status` ADD `owner_user` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Usuario que cambia el flag' AFTER `owner_date`;

-- admin
ALTER TABLE `contract_status` ADD `admin_date` DATE NULL DEFAULT NULL COMMENT 'Fecha que se cambia el flag' AFTER `admin`;
ALTER TABLE `contract_status` ADD `admin_user` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Usuario que cambia el flag' AFTER `admin_date`;

-- ready
ALTER TABLE `contract_status` ADD `ready_date` DATE NULL DEFAULT NULL COMMENT 'Fecha que se cambia el flag' AFTER `ready`;
ALTER TABLE `contract_status` ADD `ready_user` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Usuario que cambia el flag' AFTER `ready_date`;

-- pdf
ALTER TABLE `contract_status` ADD `pdf_date` DATE NULL DEFAULT NULL COMMENT 'Fecha que se cambia el flag' AFTER `pdf`;
ALTER TABLE `contract_status` ADD `pdf_user` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Usuario que cambia el flag' AFTER `pdf_date`;

-- recieved
ALTER TABLE `contract_status` ADD `recieved_date` DATE NULL DEFAULT NULL COMMENT 'Fecha que se cambia el flag' AFTER `recieved`;
ALTER TABLE `contract_status` ADD `recieved_user` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Usuario que cambia el flag' AFTER `recieved_date`;

-- payed
ALTER TABLE `contract_status` ADD `payed_date` DATE NULL DEFAULT NULL COMMENT 'Fecha que se cambia el flag' AFTER `payed`;
ALTER TABLE `contract_status` ADD `payed_user` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Usuario que cambia el flag' AFTER `payed_date`;

-- prepay
ALTER TABLE `contract_status` ADD `prepay_date` DATE NULL DEFAULT NULL COMMENT 'Fecha que se cambia el flag' AFTER `prepay`;
ALTER TABLE `contract_status` ADD `prepay_user` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Usuario que cambia el flag' AFTER `prepay_date`;

-- closed
ALTER TABLE `contract_status` ADD `closed_date` DATE NULL DEFAULT NULL COMMENT 'Fecha que se cambia el flag' AFTER `closed`;
ALTER TABLE `contract_status` ADD `closed_user` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Usuario que cambia el flag' AFTER `closed_date`;

-- constrains
ALTER TABLE `contract_status` ADD FOREIGN KEY (`contract`) REFERENCES `contract`(`project`) ON UPDATE CASCADE ON DELETE RESTRICT;


-- constrains and spelling fix
ALTER TABLE `contract_status` CHANGE `recieved` `received` INT(1) DEFAULT 0 NOT NULL COMMENT 'Se ha recibido el contrato firmado', CHANGE `recieved_date` `received_date` DATE NULL COMMENT 'Fecha que se cambia el flag', CHANGE `recieved_user` `received_user` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci NULL COMMENT 'Usuario que cambia el flag', ADD FOREIGN KEY (`owner_user`) REFERENCES `user`(`id`) ON UPDATE CASCADE;
ALTER TABLE `contract_status` ADD FOREIGN KEY (`admin_user`) REFERENCES `user`(`id`) ON UPDATE CASCADE;
ALTER TABLE `contract_status` ADD FOREIGN KEY (`pdf_user`) REFERENCES `user`(`id`) ON UPDATE CASCADE;
ALTER TABLE `contract_status` ADD FOREIGN KEY (`payed_user`) REFERENCES `user`(`id`) ON UPDATE CASCADE;
ALTER TABLE `contract_status` ADD FOREIGN KEY (`prepay_user`) REFERENCES `user`(`id`) ON UPDATE CASCADE, ADD FOREIGN KEY (`closed_user`) REFERENCES `user`(`id`) ON UPDATE CASCADE;
ALTER TABLE `contract_status` ADD FOREIGN KEY (`ready_user`) REFERENCES `user`(`id`) ON UPDATE CASCADE;
ALTER TABLE `contract_status` ADD FOREIGN KEY (`received_user`) REFERENCES `user`(`id`) ON UPDATE CASCADE;
