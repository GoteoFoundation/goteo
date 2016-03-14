CREATE TABLE `event` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` char(20) NOT NULL DEFAULT 'communication',
  `action` char(100) NOT NULL,
  `hash` char(32) NOT NULL,
  `result` char(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `finalized` datetime DEFAULT NULL,
  `succeeded` tinyint(1) DEFAULT '0',
  `error` char(255) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `hash` (`hash`),
  KEY `succeeded` (`succeeded`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
