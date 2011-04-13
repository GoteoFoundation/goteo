CREATE TABLE IF NOT EXISTS support (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  project varchar(50) NOT NULL,
  support tinytext NOT NULL,
  description text NOT NULL,
  `type` varchar(50) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY id (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Colaboraciones';

-- Alteraciones de la tabla original por si no se puede pasar el create de arriba
-- Cambiando ids numéricos por SERIAL
ALTER TABLE `support` CHANGE `id` `id` SERIAL NOT NULL AUTO_INCREMENT ;