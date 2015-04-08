CREATE TABLE `project_location` (
  `id` varchar(50) NOT NULL,
  `latitude` decimal(16,14) NOT NULL,
  `longitude` decimal(16,14) NOT NULL,
  `method` varchar(50) NOT NULL DEFAULT 'ip',
  `locable` tinyint(1) NOT NULL DEFAULT '0',
  `city` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `country` varchar(150) DEFAULT NULL,
  `country_code` varchar(2) NOT NULL,
  `info` varchar(255) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`project`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  CONSTRAINT `project_location_ibfk_1` FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

