CREATE TABLE `invest_detail` (
`invest` BIGINT NOT NULL ,
`type` VARCHAR( 30 ) NOT NULL ,
`log` TEXT NOT NULL ,
`date` TIMESTAMP NOT NULL ,
INDEX ( `invest` )
) ENGINE = InnoDB COMMENT = 'Detalles de los aportes';

-- clave primaria
ALTER TABLE `invest_detail` ADD UNIQUE `invest_type` ( `invest` , `type` );