CREATE TABLE IF NOT EXISTS `project_image` (
  `project_id` varchar(50) NOT NULL,
  `image_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`project_id`,`image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;