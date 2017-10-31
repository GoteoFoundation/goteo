CREATE TABLE IF NOT EXISTS call_sphere (
  `call` varchar(50) NOT NULL,
  `sphere` int(12) NOT NULL,
  UNIQUE KEY `call_sphere` (`call`,`sphere`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Ámbito de convocatorias';

ALTER TABLE `call_sphere` ADD `order` SMALLINT UNSIGNED NOT NULL DEFAULT '1' AFTER `sphere`;
