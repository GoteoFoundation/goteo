CREATE TABLE IF NOT EXISTS `project_image` (
  `project` varchar(50) NOT NULL,
  `image` int(10) unsigned NOT NULL,
  `section` varchar(50) DEFAULT NULL,
  `url` tinytext,
  `order` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`project`,`image`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;