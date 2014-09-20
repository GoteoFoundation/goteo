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
