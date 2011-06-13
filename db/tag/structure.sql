CREATE TABLE  `goteo`.`tag` (
`id` SERIAL NOT NULL ,
`tag` TINYTEXT NOT NULL ,
`blog` BIGINT( 20 ) UNSIGNED NOT NULL
) ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT =  'Tags de blogs (de nodo)';