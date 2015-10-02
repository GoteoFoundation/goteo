CREATE TABLE IF NOT EXISTS `page` (
`id` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
`name` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
`description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci ,
`url` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci ,
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Páginas institucionales';