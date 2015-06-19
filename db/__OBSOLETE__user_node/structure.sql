CREATE TABLE IF NOT EXISTS `user_node` (
  `user` varchar(50) NOT NULL,
  `node` varchar(50) NOT NULL,
  PRIMARY KEY (`user`,`node`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--- Import data to user role
UPDATE user_role a
INNER JOIN user_node b ON
    a.`user_id`=b.`user`
SET a.`node_id` = b.`node`;

--- user node obsolete
DROP TABLE user_node;
