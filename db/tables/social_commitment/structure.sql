CREATE TABLE `social_commitment`(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` CHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `image` CHAR( 255 ) NULL DEFAULT NULL,
    `modified` TIMESTAMP NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Compromiso social';
