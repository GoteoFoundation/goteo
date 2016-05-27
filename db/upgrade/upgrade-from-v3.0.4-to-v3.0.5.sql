--  Password length for strong security
ALTER TABLE `user` CHANGE `password` `password` VARCHAR(255) NOT NULL;
