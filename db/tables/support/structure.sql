CREATE TABLE IF NOT EXISTS support (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  project varchar(50) NOT NULL,
  support tinytext,
  description text,
  `type` varchar(50) DEFAULT NULL,
  `thread` bigint(20) unsigned DEFAULT NULL COMMENT 'De la tabla message',
  PRIMARY KEY (id),
  UNIQUE KEY id (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Colaboraciones';

-- Alteraciones de la tabla original por si no se puede pasar el create de arriba
-- Cambiando ids numéricos por SERIAL
ALTER TABLE `support` CHANGE `id` `id` SERIAL NOT NULL AUTO_INCREMENT ;

-- Para marcar el mensaje que inicia la conversación sobre la colaboración
ALTER TABLE `support` ADD `thread` BIGINT UNSIGNED NULL COMMENT 'De la tabla message';

-- Indice para hilos de mensajes
ALTER TABLE `support` ADD INDEX `hilo` ( `thread` );
ALTER TABLE `support` ADD INDEX `proyecto` ( `project` );


-- Create foreign indexs
UPDATE support SET thread=NULL WHERE thread NOT IN (SELECT id FROM message);
ALTER TABLE `support` ADD FOREIGN KEY (`thread`) REFERENCES `message`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
DELETE FROM support WHERE `project` NOT IN (SELECT id FROM `project`);
ALTER TABLE `support` ADD FOREIGN KEY (`project`) REFERENCES `project`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
