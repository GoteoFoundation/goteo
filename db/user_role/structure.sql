CREATE TABLE IF NOT EXISTS `user_role` (
  `user_id` varchar(50) NOT NULL,
  `role_id` varchar(50) NOT NULL,
  `node_id` varchar(50) NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `user_FK` (`user_id`),
  KEY `role_FK` (`role_id`),
  KEY `node_FK` (`node_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
