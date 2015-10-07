CREATE TABLE `invest_detail` (
`invest` BIGINT NOT NULL ,
`type` VARCHAR( 30 ) NOT NULL ,
`log` TEXT NOT NULL ,
`date` TIMESTAMP NOT NULL ,
INDEX ( `invest` )
) ENGINE = InnoDB COMMENT = 'Detalles de los aportes';

-- clave primaria
ALTER TABLE `invest_detail` ADD UNIQUE `invest_type` ( `invest` , `type` );

-- constrains
DELETE FROM invest_detail WHERE invest NOT IN (SELECT id FROM invest);

ALTER TABLE `invest_detail` CHANGE `invest` `invest` BIGINT(20) UNSIGNED NOT NULL, ADD FOREIGN KEY (`invest`) REFERENCES `invest`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

