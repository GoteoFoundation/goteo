DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` varchar(50) COLLATE utf8_general_ci NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  `name` varchar(100) COLLATE utf8_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_general_ci NOT NULL,
  `password` varchar(40) COLLATE utf8_general_ci NOT NULL,
  `about` text COLLATE utf8_general_ci,
  `keywords` tinytext COLLATE utf8_general_ci,
  `active` tinyint(1) NOT NULL,
  `avatar` int(11) DEFAULT NULL,
  `contribution` text COLLATE utf8_general_ci,
  `twitter` tinytext COLLATE utf8_general_ci,
  `facebook` tinytext COLLATE utf8_general_ci,
  `linkedin` tinytext COLLATE utf8_general_ci,
  `worth` int(7) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_FK` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
