CREATE TABLE `user_login` (
  `user` VARCHAR(50) NOT NULL,
  `provider` VARCHAR(50) NOT NULL,
  `oauth_token` text NOT NULL,
  `oauth_token_secret` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`user`,`provider`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- alters
-- añadido campo datetime con current timestamp