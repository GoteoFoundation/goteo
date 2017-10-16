CREATE TABLE `message_user`( `message_id` BIGINT UNSIGNED NOT NULL, `user_id` CHAR(50) NOT NULL, PRIMARY KEY (`message_id`, `user_id`), FOREIGN KEY (`message_id`) REFERENCES `message`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON UPDATE CASCADE ON DELETE CASCADE );

