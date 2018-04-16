CREATE TABLE `milestone` (
`id` SERIAL NOT NULL ,
`type` VARCHAR( 255 ),
`link` VARCHAR( 255 ),
`description` TEXT,
`image` VARCHAR( 255 ) NULL DEFAULT NULL,
`image_emoji` VARCHAR( 255 ) NULL DEFAULT NULL,
`twitter_msg` TEXT NULL DEFAULT NULL,
`facebook_msg` TEXT NULL DEFAULT NULL,
`twitter_msg_owner` TEXT NULL DEFAULT NULL,
`facebook_msg_owner` TEXT NULL DEFAULT NULL,
PRIMARY KEY ( `id` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Milestones';


-- constrains

DELETE FROM project_milestone WHERE post NOT IN (SELECT id FROM post);
DELETE FROM project_milestone WHERE project NOT IN (SELECT id FROM project);
ALTER TABLE `project_milestone` CHANGE `milestone` `milestone` BIGINT(20) UNSIGNED NULL, CHANGE `post` `post` BIGINT(20) UNSIGNED NULL, ADD FOREIGN KEY (`project`) REFERENCES `project`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD FOREIGN KEY (`milestone`) REFERENCES `milestone`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD FOREIGN KEY (`post`) REFERENCES `post`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
