CREATE TABLE `log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `scope` varchar(50) NOT NULL,
  `target_type` varchar(10) DEFAULT NULL COMMENT 'tipo de objetivo',
  `target_id` varchar(50) DEFAULT NULL COMMENT 'registro objetivo',
  `text` text DEFAULT NULL,
  `url` tinytext,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Log de cosas';

