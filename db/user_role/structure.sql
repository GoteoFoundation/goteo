CREATE TABLE IF NOT EXISTS `user_role` (
  `user_id` varchar(50) NOT NULL,
  `role_id` varchar(50) NOT NULL,
  `node_id` varchar(50) NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `user_FK` (`user_id`),
  KEY `role_FK` (`role_id`),
  KEY `node_FK` (`node_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- roles for nodes
ALTER TABLE `user_role`
    ADD COLUMN `node_id` varchar(50)  COLLATE utf8_general_ci NOT NULL after `role_id` ,
    CHANGE `datetime` `datetime` timestamp   NULL DEFAULT CURRENT_TIMESTAMP after `node_id` ,
    ADD KEY `node_FK`(`node_id`) ,
    ADD KEY `role_FK`(`role_id`) ,
    DROP KEY `role_id` ,
    ADD KEY `user_FK`(`user_id`) ,
    DROP FOREIGN KEY `user_role_ibfk_1`  ,
    DROP FOREIGN KEY `user_role_ibfk_2`  ;
