DROP TABLE IF EXISTS `user_image`;
CREATE TABLE IF NOT EXISTS `user_image` (
  `user` varchar(50) COLLATE utf8_general_ci NOT NULL,
  `image` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user`,`image`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
