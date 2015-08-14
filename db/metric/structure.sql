CREATE TABLE `metric` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `metric` char(255) NOT NULL,
  `desc` char(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `metric` (`metric`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

