CREATE TABLE IF NOT EXISTS user_interest (
  `user` varchar(50) NOT NULL,
  interest int(12) NOT NULL,
  UNIQUE KEY user_interest (`user`,interest)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Intereses de los usuarios';


-- indice
ALTER TABLE `user_interest` ADD INDEX `interes` ( `interest` ) ;