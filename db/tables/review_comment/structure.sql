CREATE TABLE `review_comment` (
`review` BIGINT UNSIGNED NOT NULL ,
`user` VARCHAR( 50 ) NOT NULL ,
`section` VARCHAR( 50 ) NOT NULL ,
`evaluation` TEXT NULL ,
`recommendation` TEXT NULL ,
`date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY ( `review` , `user` , `section` )
) ENGINE = InnoDB COMMENT = 'Comentarios de revision';