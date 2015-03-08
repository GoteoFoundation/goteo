CREATE TABLE IF NOT EXISTS `user_pool` (
  `user` varchar(50) NOT NULL,
  `amount` int(7) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `user_pool`
  ADD CONSTRAINT `user_pool_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`);
