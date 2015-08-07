CREATE TABLE `mail_stats_location` (
  `id` bigint(20) unsigned NOT NULL,
  `latitude` decimal(16,14) NOT NULL,
  `longitude` decimal(16,14) NOT NULL,
  `method` varchar(50) NOT NULL DEFAULT 'ip',
  `locable` tinyint(1) NOT NULL DEFAULT '0',
  `city` varchar(255) NOT NULL,
  `region` varchar(255) NOT NULL,
  `country` varchar(150) NOT NULL,
  `country_code` varchar(2) NOT NULL,
  `info` varchar(255) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `locable` (`locable`),
  CONSTRAINT `mail_stats_location_ibfk_1` FOREIGN KEY (`id`) REFERENCES `mail_stats` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;