CREATE TABLE IF NOT EXISTS `page_node` (
`page` varchar(50) NOT NULL,
`node` VARCHAR( 50 ) NOT NULL ,
`lang` VARCHAR( 2 ) NOT NULL ,
`content` LONGTEXT NULL,
  UNIQUE KEY `page` (`page`,`node`,`lang`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Contenidos de las paginas';

-- Titulo y cabecera en el registro del nodo
ALTER TABLE `page_node` ADD `name` TINYTEXT NULL AFTER `lang` ,
ADD `description` TEXT NULL AFTER `name`;

-- pendiente de traducir
ALTER TABLE `page_node` ADD `pending` INT( 1 ) NULL DEFAULT '0' COMMENT 'Debe revisarse la traducción';


--constrains
ALTER TABLE `page_node` ADD FOREIGN KEY (`node`) REFERENCES `node`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT;
