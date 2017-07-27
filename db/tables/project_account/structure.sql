CREATE TABLE `project_account` (
`project` VARCHAR( 50 ) NOT NULL ,
`bank` TINYTEXT NULL ,
`paypal` TINYTEXT NULL ,
PRIMARY KEY ( `project` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Cuentas bancarias de proyecto';

-- Alters
ALTER TABLE `project_account` ADD `bank_owner` TINYTEXT NULL AFTER `bank` ;
ALTER TABLE `project_account` ADD `paypal_owner` TINYTEXT NULL AFTER `paypal` ;


-- flag para permitir paypal
ALTER TABLE `project_account` ADD `allowpp` INT(1) ;

-- porcentaje de comisión goteo
ALTER TABLE `project_account` ADD `fee` INT(1) NOT NULL DEFAULT 8 COMMENT 'porcentaje de comisión goteo';

ALTER TABLE `project_account` CHANGE `fee` `fee` INT( 1 ) NOT NULL DEFAULT '4' COMMENT 'porcentaje de comisión goteo';


-- (Value Added Tax) to apply in the financial report
ALTER TABLE `project_account` ADD `vat` INT(2) NOT NULL DEFAULT 21 COMMENT '(Value Added Tax) to apply in the financial report';


-- constrains
ALTER TABLE `project_account` ADD FOREIGN KEY (`project`) REFERENCES `project`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

-- Skip login for invest
ALTER TABLE `project_account` ADD `skip_login` INT(1) DEFAULT 0 NOT NULL AFTER `vat`;
