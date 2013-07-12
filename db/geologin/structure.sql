CREATE TABLE `geologin` (
  `user` varchar(50) NOT NULL DEFAULT '',
  `ip` varchar(15) NOT NULL,
  `lon` decimal(14,12) DEFAULT NULL,
  `lat` decimal(14,12) DEFAULT NULL,
  `msg` tinytext,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `assigned` int(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Guarda dats de login';
