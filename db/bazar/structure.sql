CREATE TABLE IF NOT EXISTS `bazar` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reward` bigint(20) unsigned NOT NULL,
  `project` varchar(50) NOT NULL,
  `title` TINYTEXT NULL ,
  `description` TEXT NULL ,
  `amount` int(5) NOT NULL,
  `image` int(10) unsigned NOT NULL,
  `order` smallint(5) NOT NULL,
  `active` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='productos del catalogo' AUTO_INCREMENT=1 ;

-- permitir campos vacios
ALTER TABLE `bazar` CHANGE `reward` `reward` BIGINT(20) UNSIGNED NULL, CHANGE `project` `project` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL, CHANGE `amount` `amount` INT(5) NULL, CHANGE `image` `image` INT(10) UNSIGNED NULL, CHANGE `order` `order` SMALLINT(5) NOT NULL DEFAULT '9999', CHANGE `active` `active` INT(1) NOT NULL DEFAULT '1';

-- campo imagen a nombre archivo
ALTER TABLE `bazar` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';


-- Charset
ALTER TABLE `bazar` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

ALTER TABLE `bazar` CHANGE `project` `project` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
CHANGE `title` `title` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
CHANGE `description` `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
CHANGE `image` `image` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';
