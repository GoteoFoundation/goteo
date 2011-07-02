CREATE TABLE `review` (
`id` SERIAL NOT NULL AUTO_INCREMENT ,
`project` VARCHAR( 50 ) NOT NULL ,
`status` BOOLEAN NOT NULL ,
`to_checker` text,
`to_owner` text,
`score` int(2) NOT NULL DEFAULT '0',
`max` int(2) NOT NULL DEFAULT '0',
PRIMARY KEY ( `id` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Revision para evaluacion de proyecto';

-- alters
ALTER TABLE `review` CHANGE `status` `status` TINYINT( 1 ) NOT NULL DEFAULT '1';