-- Pool transactions table
CREATE TABLE `pool_transaction`( `id` BIGINT UNSIGNED NOT NULL, `invest_id` BIGINT, `user_id` CHAR(50) NOT NULL, `amount` INT NOT NULL, `date` DATETIME, `modified` TIMESTAMP NOT NULL, PRIMARY KEY (`id`), FOREIGN KEY (`invest_id`) REFERENCES `invest`(`id`) ON UPDATE CASCADE, FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON UPDATE CASCADE );
