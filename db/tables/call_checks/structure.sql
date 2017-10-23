CREATE TABLE `call_check` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`call` varchar(50) NOT NULL,
`description` TEXT DEFAULT NULL,
PRIMARY KEY ( `id` ),
CONSTRAINT `call` FOREIGN KEY (`call`) 
		REFERENCES `call`(`id`) 
	    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Call check';