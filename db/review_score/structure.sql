CREATE TABLE `review_score` (
`review` BIGINT UNSIGNED NOT NULL ,
`user` VARCHAR( 50 ) NOT NULL ,
`criteria` BIGINT UNSIGNED NOT NULL ,
`score` BOOLEAN NOT NULL DEFAULT '0',
PRIMARY KEY ( `review` , `user` , `criteria` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Puntuacion por citerio';