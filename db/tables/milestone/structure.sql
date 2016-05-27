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
