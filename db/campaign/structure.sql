CREATE TABLE `goteo`.`campaign` (
`id` SERIAL NOT NULL ,
`name` TINYTEXT NOT NULL ,
PRIMARY KEY ( `id` )
) ENGINE = InnoDB;

-- alters
ALTER TABLE `campaign` ADD `description` TEXT NULL ;