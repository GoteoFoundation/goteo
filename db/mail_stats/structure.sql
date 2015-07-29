CREATE TABLE `mail_stats` (
  `mail_id` bigint(20) unsigned NOT NULL,
  `email` char(150) NOT NULL,
  `metric` char(255) NOT NULL DEFAULT 'opened',
  `counter` int(10) unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `modified_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`mail_id`,`email`,`metric`),
  KEY `email` (`email`),
  KEY `metric` (`metric`),
  CONSTRAINT `mail_stats_ibfk_1` FOREIGN KEY (`mail_id`) REFERENCES `mail` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8