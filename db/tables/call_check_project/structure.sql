CREATE TABLE `call_check_project`( `id` INT(10) NOT NULL, 
									`call_check` INT(10) UNSIGNED NOT NULL, 
									`response` TEXT, PRIMARY KEY (`id`), 
									CONSTRAINT `call_check` FOREIGN KEY (`call_check`) 
									REFERENCES `call_check`(`id`) 
								ON UPDATE CASCADE ON DELETE CASCADE ); 
                         

