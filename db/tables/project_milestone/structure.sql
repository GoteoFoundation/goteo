CREATE TABLE IF NOT EXISTS project_milestone (
 	`id` SERIAL NOT NULL ,
 	`project` varchar(50) NOT NULL,
  	`milestone` int(12) DEFAULT NULL,
  	`date` date,
  	`post` int(12) DEFAULT NULL,
  	PRIMARY KEY  ( `id` )
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Project milestones';

