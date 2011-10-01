CREATE TABLE IF NOT EXISTS `user_prefer` (
  `user` varchar(50) NOT NULL,
  `updates` int(1) NOT NULL DEFAULT 0,
  `threads` int(1) NOT NULL DEFAULT 0,
  `rounds` int(1) NOT NULL DEFAULT 0,
  `selfproj` int(1) NOT NULL DEFAULT 0,
  `anymail` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Preferencias de notificación de usuario';