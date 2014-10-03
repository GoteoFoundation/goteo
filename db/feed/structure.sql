CREATE TABLE `feed` (
`id` SERIAL NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`title` TINYTEXT NOT NULL ,
`url` TINYTEXT DEFAULT NULL ,
`datetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
`scope` VARCHAR( 50 ) NOT NULL ,
`type` VARCHAR( 50 ) NOT NULL ,
`html` TEXT NOT NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Log de eventos';

-- alters
ALTER TABLE `feed` CHANGE `url` `url` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE `feed` ADD `image` INT( 10 ) NULL ;

-- elemento al que afecta el evento (e indice sobr el tipo)
ALTER TABLE `feed` ADD `target_type` VARCHAR( 10 ) NULL COMMENT 'tipo de objetivo',
ADD `target_id` VARCHAR( 50 ) NULL COMMENT 'registro objetivo',
ADD INDEX ( `target_type` );

-- campo imagen a nombre archivo
ALTER TABLE `feed` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

-- entrada de blog asociada para sacar datos en misma consulta
ALTER TABLE `feed` ADD `post` INT( 20 ) UNSIGNED NULL DEFAULT NULL COMMENT 'Entrada de blog';


--- Update para rellenar ese campo
-- SELECT id, url, type, scope, SUBSTRING(url, 7) FROM `feed` WHERE url like '/blog/%';
UPDATE `feed` SET post = SUBSTRING(url, 7) WHERE  url like '/blog/%';
