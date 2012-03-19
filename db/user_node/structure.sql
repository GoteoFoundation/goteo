CREATE TABLE IF NOT EXISTS `user_node` (
  `user` varchar(50) NOT NULL,
  `node` varchar(50) NOT NULL,
  PRIMARY KEY (`user`,`node`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
