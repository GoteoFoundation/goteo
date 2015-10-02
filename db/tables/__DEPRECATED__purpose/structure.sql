CREATE TABLE IF NOT EXISTS purpose (
  `text` varchar(50) NOT NULL,
  purpose TEXT NOT NULL,
  html tinyint(1) DEFAULT NULL COMMENT 'Si el texto lleva formato html',
  `group` varchar(50) NOT NULL DEFAULT 'general' COMMENT 'Agrupacion de uso',
PRIMARY KEY (`text`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Explicación del propósito de los textos';

-- Si no se puede pasar de nuevo el create, pasar el alter table
ALTER TABLE `purpose` ADD `html` BOOLEAN NULL COMMENT 'Si el texto lleva formato html';

-- Agrupacion de usos de los textos
ALTER TABLE `purpose` ADD `group` VARCHAR( 50 ) NOT NULL DEFAULT 'general' COMMENT 'Agrupacion de uso';

-- Textos mas largos en el proposito
ALTER TABLE `purpose` CHANGE `purpose` `purpose` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL 