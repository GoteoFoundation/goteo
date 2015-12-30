CREATE TABLE IF NOT EXISTS invest_node (
  `user_id` varchar(50) NOT NULL,
  `user_node` varchar(50) NOT NULL,
  `project_id` varchar(50) NOT NULL,
  `project_node` varchar(50) NOT NULL,
  `invest_id` bigint(20) NOT NULL,
  `invest_node` varchar(50) NOT NULL COMMENT 'Nodo en el que se hace el aporte',
  UNIQUE KEY `invest` (`invest_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Aportes por usuario/nodo a proyecto/nodo';

-- Optimizations:
ALTER TABLE `invest_node`
    ADD KEY `invest_id`(`invest_id`) ,
    ADD KEY `invest_node`(`invest_node`) ,
    ADD KEY `project_id`(`project_id`) ,
    ADD KEY `project_node`(`project_node`) ,
    ADD KEY `user_id`(`user_id`) ;

-- constrains
ALTER TABLE `invest_node` ADD FOREIGN KEY (`user_node`) REFERENCES `node`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
                          ADD FOREIGN KEY (`project_node`) REFERENCES `node`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
                          ADD FOREIGN KEY (`invest_node`) REFERENCES `node`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT;

DELETE FROM invest_node WHERE invest_id NOT IN (SELECT id FROM invest);

ALTER TABLE `invest_node` CHANGE `invest_id` `invest_id` BIGINT(20) UNSIGNED NOT NULL, ADD FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD FOREIGN KEY (`project_id`) REFERENCES `project`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD FOREIGN KEY (`invest_id`) REFERENCES `invest`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

-- Allow null in project for pool recharges
ALTER TABLE `invest_node` CHANGE `project_id` `project_id` varchar(50), CHANGE `project_node` `project_node` varchar(50);
