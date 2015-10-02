DROP TABLE IF EXISTS `user_api`;
CREATE TABLE IF NOT EXISTS `user_api` (
  `user_id` varchar(50) NOT NULL,
  `key` varchar(50) NOT NULL,
  `expiration_date` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `user_api`
  ADD CONSTRAINT `user_api_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
