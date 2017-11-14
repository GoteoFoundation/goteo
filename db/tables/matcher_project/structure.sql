CREATE TABLE `matcher_project` (
  `matcher_id` varchar(50) NOT NULL,
  `project_id` varchar(50) NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'pending' COMMENT 'pending, accepted, active (funding ok), rejected (discarded by user), discarded (by admin)',
  PRIMARY KEY (`matcher_id`,`project_id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `matcher_project_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `matcher_project_ibfk_2` FOREIGN KEY (`matcher_id`) REFERENCES `matcher` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;
