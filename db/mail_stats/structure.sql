CREATE TABLE `mail_stats` (
  `mail_id` bigint(20) unsigned NOT NULL,
  `email` char(150) NOT NULL,
  `metric_id` bigint(20) unsigned NOT NULL,
  `counter` int(10) unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `modified_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`mail_id`,`email`,`metric_id`),
  KEY `email` (`email`),
  KEY `metric` (`metric_id`),
  KEY `mail_id` (`mail_id`),
  CONSTRAINT `mail_stats_ibfk_1` FOREIGN KEY (`metric_id`) REFERENCES `metric` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- id primary needed
ALTER TABLE `mail_stats` ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT FIRST, DROP PRIMARY KEY, ADD PRIMARY KEY (`id`, `mail_id`, `email`, `metric_id`);
