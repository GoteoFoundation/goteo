CREATE TABLE IF NOT EXISTS charge (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  invest int(11) NOT NULL,
  entity varchar(50) NOT NULL,
  `code` varchar(256) NOT NULL,
  `date` date NOT NULL,
  result varchar(8) NOT NULL COMMENT 'FAIL / SUCCESS',
  PRIMARY KEY (id),
  UNIQUE KEY id (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Transacciones en banco o paypal';

-- Alteraciones de la tabla original por si no se puede pasar el create de arriba
-- Cambiando ids numéricos por SERIAL
ALTER TABLE `charge` CHANGE `id` `id` SERIAL NOT NULL AUTO_INCREMENT ;

-- Campo para el PayKey quedará vacio si falla
ALTER TABLE `charge` CHANGE `code` `code` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'PayKey';
