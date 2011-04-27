CREATE TABLE `goteo`.`page` (
`id` TINYINT( 4 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
`description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci ,
`url` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci ,
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Páginas institucionales';
