CREATE TABLE IF NOT EXISTS `user` (
  id varchar(50) NOT NULL,
  `name` varchar(256) NOT NULL,
  email varchar(256) NOT NULL,
  `password` varchar(32) NOT NULL,
  about text,
  signup date NOT NULL,
  active tinyint(1) NOT NULL DEFAULT '0',
  avatar tinytext,
  contribution text,
  blog varchar(256) DEFAULT NULL,
  twitter varchar(256) DEFAULT NULL,
  facebook varchar(256) DEFAULT NULL,
  linkedin varchar(256) DEFAULT NULL,
  worth int(7) DEFAULT NULL,
  lastedit date DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Usuarios';

-- Modificaciones
ALTER TABLE `user` ADD `user` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'login' AFTER `id` ;
ALTER TABLE `user` CHANGE `active` `active` TINYINT( 1 ) NOT NULL DEFAULT '1';
