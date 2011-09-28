CREATE TABLE `project_account` (
`project` VARCHAR( 50 ) NOT NULL ,
`bank` TINYTEXT NULL ,
`paypal` TINYTEXT NULL ,
PRIMARY KEY ( `project` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Cuentas bancarias de proyecto';