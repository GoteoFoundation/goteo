DROP TABLE IF EXISTS `user_image`;
CREATE TABLE IF NOT EXISTS `user_image` (
  `user_id` varchar(50) COLLATE utf8_general_ci NOT NULL,
  `image_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`image_id`),
  KEY `user_FK` (`user_id`),
  KEY `image_FK` (`image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
