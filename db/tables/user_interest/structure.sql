CREATE TABLE IF NOT EXISTS user_interest (
  `user` varchar(50) NOT NULL,
  interest int(12) NOT NULL,
  UNIQUE KEY user_interest (`user`,interest)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Intereses de los usuarios';


-- indice
ALTER TABLE `user_interest` ADD INDEX `interes` ( `interest` ) ;

-- user interests constrains
DELETE FROM user_interest WHERE `user` NOT IN (SELECT id FROM `user`);
ALTER TABLE `user_interest` CHANGE `interest` `interest` INT(10) UNSIGNED NOT NULL, ADD FOREIGN KEY (`user`) REFERENCES `user`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD FOREIGN KEY (`interest`) REFERENCES `category`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
