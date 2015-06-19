CREATE TABLE IF NOT EXISTS `user_role` (
  `user_id` varchar(50) NOT NULL,
  `role_id` varchar(50) NOT NULL,
  `node_id` varchar(50) NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `user_FK` (`user_id`),
  KEY `role_FK` (`role_id`),
  KEY `node_FK` (`node_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Correct indexs
ALTER TABLE `user_role`
    CHANGE `node_id` `node_id` varchar(50)  COLLATE utf8_general_ci NULL after `role_id` ,
    ADD COLUMN `datetime` timestamp   NULL DEFAULT CURRENT_TIMESTAMP after `node_id` ,
    DROP KEY `node_FK` ,
    ADD KEY `node_id`(`node_id`) ,
    DROP KEY `PRIMARY` ,
    DROP KEY `role_FK` ,
    ADD KEY `role_id`(`role_id`) ,
    DROP KEY `user_FK` ,
    ADD KEY `user_id`(`user_id`) ;

-- set null where node is empty
UPDATE user_role SET node_id = NULL WHERE node_id = '';

-- set foreign keys
ALTER TABLE `user_role`
    ADD CONSTRAINT `user_role_ibfk_1`
    FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ,
    ADD CONSTRAINT `user_role_ibfk_2`
    FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ,
    ADD CONSTRAINT `user_role_ibfk_3`
    FOREIGN KEY (`node_id`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;
