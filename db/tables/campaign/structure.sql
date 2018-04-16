CREATE TABLE `campaign` (
`id` SERIAL NOT NULL ,
`node` VARCHAR( 50 ) NOT NULL ,
`call` VARCHAR( 50 ) NOT NULL ,
`active` INT(1) NOT NULL DEFAULT '0' ,
`order` SMALLINT UNSIGNED NOT NULL DEFAULT '1',
 UNIQUE KEY `call_node` (`node`,`call`),
  PRIMARY KEY `id` (`id`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Convocatorias en portada';

-- constrains
ALTER TABLE `campaign` ADD FOREIGN KEY (`node`) REFERENCES `node`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `campaign` ADD FOREIGN KEY (`call`) REFERENCES `call`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

