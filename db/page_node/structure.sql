CREATE TABLE IF NOT EXISTS `page_node` (
`page` varchar(50) NOT NULL,
`node` VARCHAR( 50 ) NOT NULL ,
`lang` VARCHAR( 2 ) NOT NULL ,
`content` LONGTEXT NULL,
  UNIQUE KEY `page` (`page`,`node`,`lang`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Contenidos de las paginas';