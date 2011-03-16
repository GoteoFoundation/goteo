CREATE TABLE IF NOT EXISTS `user` (
  id varchar(50) NOT NULL,
  `name` tinytext NOT NULL,
  email tinytext NOT NULL,
  `password` varchar(32) NOT NULL,
  about text,
  signup date NOT NULL,
  active tinyint(1) NOT NULL DEFAULT '0',
  avatar tinytext,
  contribution text,
  blog tinytext,
  twitter tinytext,
  facebook tinytext,
  linkedin tinytext,
  worth int(12) DEFAULT NULL,
  lastedit date DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Usuarios';
