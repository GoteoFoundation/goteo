CREATE TABLE IF NOT EXISTS invest (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(50) NOT NULL,
  project varchar(50) NOT NULL,
  amount int(6) NOT NULL,
  `status` int(1) NOT NULL COMMENT '0 pendiente, 1 cobrado, 2 devuelto',
  anonymous tinyint(1) DEFAULT NULL,
  resign tinyint(1) DEFAULT NULL,
  invested date DEFAULT NULL,
  charged date DEFAULT NULL,
  returned date DEFAULT NULL,
  `code` varchar(256) DEFAULT NULL COMMENT 'PreapprovalKey',
  PRIMARY KEY (id),
  UNIQUE KEY id (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Aportes monetarios a proyectos';

-- Alteraciones de la tabla original por si no se puede pasar el create de arriba
-- Cambiando ids numéricos a SERIAL
ALTER TABLE `invest` CHANGE `id` `id` SERIAL NOT NULL AUTO_INCREMENT;
-- campo para guardar el codigo preapproval
ALTER TABLE `invest` ADD `code` VARCHAR( 256 ) NULL COMMENT 'PreapprovalKey';