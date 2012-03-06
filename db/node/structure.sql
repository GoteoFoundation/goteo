CREATE TABLE IF NOT EXISTS node (
  id varchar(50) NOT NULL,
  `name` varchar(256) NOT NULL,
  active tinyint(1) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Nodos';

-- Alters
ALTER TABLE `node` ADD `url` VARCHAR( 255 ) NOT NULL;
