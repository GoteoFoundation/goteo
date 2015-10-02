CREATE TABLE IF NOT EXISTS `user_vip` (
  `user` varchar(50) COLLATE utf8_general_ci NOT NULL,
  `image` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Datos usuario colaborador';

-- campo imagen a nombre archivo
ALTER TABLE `user_vip` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';
