CREATE TABLE `matcher_user` (
  `matcher_id` varchar(50) NOT NULL COMMENT 'Matcher campaign',
  `user_id` varchar(50) NOT NULL COMMENT 'User owner',
  `pool` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Use owner''s pool as funding source',
  PRIMARY KEY (`matcher_id`,`user_id`),
  KEY `matcher_user_ibfk_1` (`user_id`),
  CONSTRAINT `matcher_user_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `matcher_user_ibfk_2` FOREIGN KEY (`matcher_id`) REFERENCES `matcher` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;
