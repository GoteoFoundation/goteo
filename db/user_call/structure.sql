CREATE TABLE IF NOT EXISTS `user_call` (
  `user` varchar(50) NOT NULL,
  `call` varchar(50) NOT NULL,
  PRIMARY KEY (`user`,`call`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'Asignacion de convocatorias a admines';
