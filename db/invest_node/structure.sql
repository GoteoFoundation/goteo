CREATE TABLE IF NOT EXISTS invest_node (
  `user_id` varchar(50) NOT NULL,
  `user_node` varchar(50) NOT NULL,
  `project_id` varchar(50) NOT NULL,
  `project_node` varchar(50) NOT NULL,
  `invest_id` bigint(20) NOT NULL,
  UNIQUE KEY `invest` (`user_id`,`user_node`,`project_id`,`project_node`,`invest_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Aportes por usuario/nodo a proyecto/nodo';
