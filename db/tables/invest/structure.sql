CREATE TABLE IF NOT EXISTS invest (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(50) NOT NULL,
  project varchar(50) NOT NULL,
  account varchar(256) NOT NULL,
  amount int(6) NOT NULL,
  `status` int(1) NOT NULL COMMENT '0 pendiente, 1 cobrado, 2 devuelto',
  anonymous tinyint(1) DEFAULT NULL,
  resign tinyint(1) DEFAULT NULL,
  invested date DEFAULT NULL,
  charged date DEFAULT NULL,
  returned date DEFAULT NULL,
  preapproval varchar(256) DEFAULT NULL COMMENT 'PreapprovalKey',
  payment varchar(256) DEFAULT NULL COMMENT 'PayKey',
  `transaction` varchar(256) DEFAULT NULL COMMENT 'TransactionId',
  `method` varchar(20) NOT NULL COMMENT 'Metodo de pago',
  PRIMARY KEY (id),
  UNIQUE KEY id (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Aportes monetarios a proyectos';

-- Alteraciones de la tabla original por si no se puede pasar el create de arriba
-- Cambiando ids numéricos a SERIAL
ALTER TABLE `invest` CHANGE `id` `id` SERIAL NOT NULL AUTO_INCREMENT;
-- campo para guardar el codigo preapproval
ALTER TABLE `invest` ADD `code` VARCHAR( 256 ) NULL COMMENT 'PreapprovalKey';
ALTER TABLE `invest` CHANGE `code` `preapproval` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'PreapprovalKey';

ALTER TABLE `invest` ADD `payment` VARCHAR( 256 ) NULL COMMENT 'PayKey';
ALTER TABLE `invest` ADD `transaction` VARCHAR( 256 ) NULL COMMENT 'PaypalId';

ALTER TABLE `invest` ADD `account` VARCHAR( 256 ) NOT NULL AFTER `project` ;

ALTER TABLE `invest` ADD `method` VARCHAR( 20 ) NOT NULL COMMENT 'Metodo de pago';

-- Para aportes manuales y aportes de campaña
ALTER TABLE `invest` ADD `admin` VARCHAR( 50 ) NULL COMMENT 'Admin que creó el aporte manual';
ALTER TABLE `invest` ADD `campaign` BIGINT UNSIGNED NULL COMMENT 'campaña de la que forma parte este dinero';

-- Para aportes de capital riego
ALTER TABLE `invest` CHANGE `campaign` `campaign` INT( 1 ) UNSIGNED NULL DEFAULT NULL COMMENT 'si es un aporte de capital riego';
ALTER TABLE `invest` ADD `drops` BIGINT( 20 ) UNSIGNED NULL DEFAULT NULL COMMENT 'id del aporte que provoca este riego';
ALTER TABLE `invest` ADD `droped` BIGINT( 20 ) UNSIGNED NULL DEFAULT NULL COMMENT 'id del riego generado por este aporte';
ALTER TABLE `invest` ADD `call` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'campaña dedonde sale el dinero';

-- para marcar incidencia
ALTER TABLE `invest` ADD `issue` INT( 1 ) NULL DEFAULT NULL COMMENT 'Problemas con el cobro del aporte';

-- indice para aportes que generan riego
ALTER TABLE `invest` ADD INDEX `convocatoria` ( `call` ) ;

-- memo conversión
ALTER TABLE `invest` ADD `amount_original` INT( 6 ) NULL DEFAULT NULL COMMENT 'Importe introducido por el usuario' AFTER `amount`;
ALTER TABLE `invest` ADD `currency` VARCHAR(4) NOT NULL DEFAULT 'EUR' COMMENT 'Divisa al aportar' AFTER `amount_original`;
ALTER TABLE `invest` ADD `currency_rate` DECIMAL(9, 5) NOT NULL DEFAULT 1 COMMENT 'Ratio de conversión a eurio al aportar' AFTER `currency`;


-- funcionalidad credito
ALTER TABLE `invest` ADD `pool` INT( 1 ) NULL DEFAULT NULL COMMENT 'A reservar si el proyecto falla';


ALTER TABLE `invest` ADD FOREIGN KEY (`user`) REFERENCES `user`(`id`) ON UPDATE CASCADE;
ALTER TABLE `invest` ADD FOREIGN KEY (`project`) REFERENCES `project`(`id`) ON UPDATE CASCADE;

-- Allow null in project for pool recharges
ALTER TABLE `invest` CHANGE `project` `project` varchar(50);

-- Invest null removal for boolean fields
UPDATE `invest` SET anonymous=0 WHERE anonymous IS NULL;
UPDATE `invest` SET resign=0 WHERE resign IS NULL;
UPDATE `invest` SET campaign=0 WHERE campaign IS NULL;
UPDATE `invest` SET pool=0 WHERE pool IS NULL;
UPDATE `invest` SET issue=0 WHERE issue IS NULL;
ALTER TABLE `invest` CHANGE `anonymous` `anonymous` BOOLEAN DEFAULT 0 NOT NULL, CHANGE `resign` `resign` BOOLEAN DEFAULT 0 NOT NULL, CHANGE `campaign` `campaign` BOOLEAN DEFAULT 0 NOT NULL COMMENT 'si es un aporte de capital riego', CHANGE `issue` `issue` BOOLEAN DEFAULT 0 NOT NULL COMMENT 'Problemas con el cobro del aporte', CHANGE `pool` `pool` BOOLEAN DEFAULT 0 NOT NULL COMMENT 'A reservar si el proyecto falla';

-- invests matcher & call
ALTER TABLE `invest` ADD COLUMN `matcher` VARCHAR(50) NULL AFTER `call`,
    ADD FOREIGN KEY (`call`) REFERENCES `call`(`id`) ON UPDATE CASCADE,
    ADD FOREIGN KEY (`matcher`) REFERENCES `matcher`(`id`) ON UPDATE CASCADE;
