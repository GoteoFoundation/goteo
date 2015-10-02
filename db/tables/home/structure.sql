CREATE TABLE `home` (
`item` VARCHAR( 10 ) NOT NULL ,
`node` VARCHAR( 50 ) NOT NULL ,
`order` SMALLINT UNSIGNED NOT NULL DEFAULT '1',
 UNIQUE KEY `item_node` (`item`, `node`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Elementos en portada';

-- Elementos en portada
ALTER TABLE `home` ADD `type` VARCHAR( 5 ) NULL DEFAULT 'main' COMMENT 'lateral o central' AFTER `item` ;

-- constrains
DELETE FROM `home` WHERE `node` NOT IN (SELECT id FROM `node`);
ALTER TABLE `home` ADD FOREIGN KEY (`node`) REFERENCES `node`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;

-- optimization
ALTER TABLE `home` CHANGE `type` `type` VARCHAR(5) CHARSET utf8 COLLATE utf8_general_ci DEFAULT 'main' NOT NULL COMMENT 'lateral o central';
