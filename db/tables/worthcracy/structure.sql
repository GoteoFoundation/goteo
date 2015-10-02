CREATE TABLE IF NOT EXISTS worthcracy (
  id int(2) NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  amount int(6) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Niveles de meritocracia';
