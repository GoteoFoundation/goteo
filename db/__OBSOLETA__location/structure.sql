CREATE TABLE `location` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `location` tinytext COLLATE utf8_general_ci DEFAULT NULL,
  `region` tinytext COLLATE utf8_general_ci DEFAULT NULL,
  `country` tinytext COLLATE utf8_general_ci NOT NULL,
  `lon` decimal(16,14) NOT NULL,
  `lat` decimal(16,14) NOT NULL,
  `valid` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Geolocalización';

-- Location modifications
ALTER IGNORE TABLE `location`
    CHANGE `location` `city` char(255)  COLLATE utf8_general_ci,
    CHANGE `region` `region` char(255)  COLLATE utf8_general_ci,
    CHANGE `country` `country` char(255)  COLLATE utf8_general_ci,
    ADD COLUMN `country_code` char(2)  COLLATE utf8_general_ci NOT NULL after `country` ,
    CHANGE `lon` `longitude` DECIMAL(16,14) NOT NULL,
    CHANGE `lat` `latitude` DECIMAL(16,14) NOT NULL,
    CHANGE `valid` `valid` tinyint(1)   NOT NULL DEFAULT 0 after `latitude` ,
    ADD COLUMN `modified` timestamp   NOT NULL DEFAULT CURRENT_TIMESTAMP  on update CURRENT_TIMESTAMP after `valid` ,
    DROP KEY `id` ,
    ADD UNIQUE KEY `location`(`longitude`,`latitude`) ,
    ADD PRIMARY KEY(`id`) ;

-- optimization
ALTER TABLE `location`
    ADD KEY `latitude`(`latitude`) ,
    ADD KEY `longitude`(`longitude`) ;
