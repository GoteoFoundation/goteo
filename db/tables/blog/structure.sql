CREATE TABLE  `blog` (
`id` SERIAL NOT NULL ,
`type` VARCHAR( 10 ) NOT NULL ,
`owner` VARCHAR( 50 ) NOT NULL COMMENT  'la id del proyecto o nodo',
`active` BOOLEAN NOT NULL DEFAULT  '1'
) ENGINE = INNODB COMMENT =  'Blogs de nodo o proyecto';