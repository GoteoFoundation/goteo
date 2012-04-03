CREATE TABLE IF NOT EXISTS `user_vip` (
  `user` varchar(50) COLLATE utf8_general_ci NOT NULL,
  `image` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Datos usuario colaborador';
