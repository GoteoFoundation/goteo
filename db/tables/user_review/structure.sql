CREATE TABLE `user_review` (
`user` VARCHAR( 50 ) NOT NULL ,
`review` BIGINT UNSIGNED NOT NULL ,
PRIMARY KEY ( `user` , `review` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Asignacion de revision a usuario';

-- alters
ALTER TABLE `user_review` ADD `ready` BOOLEAN NOT NULL DEFAULT '0' COMMENT 'Ha terminado con la revision';