CREATE TABLE IF NOT EXISTS `user_project` (
  `user` varchar(50) NOT NULL,
  `project` varchar(50) NOT NULL,
  UNIQUE KEY `user` (`user`,`project`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- constrains
ALTER TABLE `user_project` ADD FOREIGN KEY (`user`) REFERENCES `user`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD FOREIGN KEY (`project`) REFERENCES `project`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
