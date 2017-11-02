CREATE TABLE IF NOT EXISTS call_icon (
  `call` varchar(50) NOT NULL,
  icon varchar(50) NOT NULL,
  UNIQUE KEY call_icon (`call`,icon)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tipos de retorno de las convocatorias';

DELETE FROM call_icon WHERE icon NOT IN (SELECT id FROM icon);
ALTER TABLE `call_icon` ADD FOREIGN KEY (`call`) REFERENCES `call`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD FOREIGN KEY (`icon`) REFERENCES `icon`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
