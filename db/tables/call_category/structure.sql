CREATE TABLE IF NOT EXISTS call_category (
  `call` varchar(50) NOT NULL,
  category int(12) NOT NULL,
  UNIQUE KEY call_category (`call`,category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Categorias de las convocatorias';

ALTER TABLE `call_category` ADD FOREIGN KEY (`call`) REFERENCES `call`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE `call_category` CHANGE `category` `category` INT(10) UNSIGNED NOT NULL, ADD FOREIGN KEY (`category`) REFERENCES `category`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
