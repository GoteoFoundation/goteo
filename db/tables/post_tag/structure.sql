CREATE TABLE  `post_tag` (
`post` BIGINT UNSIGNED NOT NULL ,
`tag` BIGINT UNSIGNED NOT NULL ,
PRIMARY KEY (  `post` ,  `tag` )
) ENGINE = INNODB COMMENT =  'Tags de las entradas';