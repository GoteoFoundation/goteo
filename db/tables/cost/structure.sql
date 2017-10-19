CREATE TABLE IF NOT EXISTS cost (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  project varchar(50) NOT NULL,
  cost varchar(256),
  description tinytext,
  `type` varchar(50) DEFAULT NULL,
  amount int(5) DEFAULT '0',
  required tinyint(1) DEFAULT '0',
  `from` date DEFAULT NULL,
  `until` date DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY id (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Desglose de costes de proyectos';


-- Alteraciones de la tabla original por si no se puede pasar el create de arriba
ALTER TABLE `cost` ADD `description` TINYTEXT NULL AFTER `cost`;
ALTER TABLE `cost` CHANGE `cost` `cost` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;
ALTER TABLE `cost` CHANGE `required` `required` TINYINT( 1 ) NULL DEFAULT '0';
ALTER TABLE `cost` CHANGE `amount` `amount` INT( 5 ) NULL DEFAULT '0';

-- Cambiando ids numéricos por SERIAL
ALTER TABLE `cost` CHANGE `id` `id` SERIAL NOT NULL AUTO_INCREMENT;

-- Costes sin tipo
ALTER TABLE `cost` CHANGE `type` `type` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ;

-- Lonmgitud de campos de texto
ALTER TABLE `cost` CHANGE `cost` `cost` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
CHANGE `description` `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;

-- add order to cost
ALTER TABLE `cost` ADD COLUMN `order` INT UNSIGNED DEFAULT 1 NOT NULL AFTER `until`;
ALTER TABLE `cost` DROP INDEX `id`, ADD INDEX (`order`);

-- constrains
DELETE FROM cost WHERE project NOT IN (SELECT id FROM project);
ALTER TABLE `cost` ADD FOREIGN KEY (`project`) REFERENCES `project`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

DELETE FROM cost_lang WHERE id NOT IN (SELECT id FROM cost);
UPDATE cost_lang a JOIN cost b ON a.id=b.id AND a.project != b.project SET a.project = b.project;
ALTER TABLE `cost_lang` CHANGE `id` `id` BIGINT(20) UNSIGNED NOT NULL, ADD FOREIGN KEY (`id`) REFERENCES `cost`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD FOREIGN KEY (`project`) REFERENCES `project`(`id`) ON UPDATE CASCADE;
