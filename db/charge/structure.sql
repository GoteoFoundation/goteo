CREATE TABLE IF NOT EXISTS charge (
  id int(11) NOT NULL AUTO_INCREMENT,
  invest int(11) NOT NULL,
  entity varchar(50) NOT NULL,
  `code` varchar(256) NOT NULL,
  `date` date NOT NULL,
  result varchar(8) NOT NULL COMMENT 'FAIL / SUCCESS',
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Transacciones en banco o paypal';
