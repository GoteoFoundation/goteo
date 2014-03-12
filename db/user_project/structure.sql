CREATE TABLE IF NOT EXISTS `user_project` (
  `user` varchar(50) NOT NULL,
  `project` varchar(50) NOT NULL,
  UNIQUE KEY `user` (`user`,`project`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
