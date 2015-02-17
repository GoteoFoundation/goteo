CREATE TABLE `location_item` (
  `location` int(20) unsigned NOT NULL,
  `item` varchar(50) CHARACTER SET utf8 NOT NULL,
  `type` varchar(7) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT='Tabla de relacion localizaciones y registros';

-- Charset
ALTER TABLE `location_item` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

-- indice
ALTER IGNORE TABLE `location_item` ADD INDEX `itemtipo` (`item`, `type`);

-- Location item modifications
ALTER IGNORE TABLE `location_item`
    ADD COLUMN `method` char(50)  COLLATE utf8_general_ci NOT NULL DEFAULT 'ip' after `type` ,
    ADD COLUMN `locable` tinyint(1)   NOT NULL DEFAULT 0 after `method` ,
    ADD COLUMN `info` char(255)  COLLATE utf8_general_ci NULL after `locable` ,
    ADD COLUMN `modified` timestamp   NOT NULL DEFAULT CURRENT_TIMESTAMP  on update CURRENT_TIMESTAMP after `info` ,
    DROP KEY `itemtipo` ,
    ADD PRIMARY KEY(`item`,`type`) ;

ALTER TABLE `location_item` ADD INDEX (`location`);
