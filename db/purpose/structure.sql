CREATE TABLE IF NOT EXISTS purpose (
  `text` varchar(50) NOT NULL,
  purpose tinytext NOT NULL,
  html tinyint(1) DEFAULT NULL COMMENT 'Si el texto lleva formato html',
  PRIMARY KEY (`text`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Explicación del propósito de los textos';

-- Si no se puede pasar de nuevo el create, pasar el alter table
ALTER TABLE `purpose` ADD `html` BOOLEAN NULL COMMENT 'Si el texto lleva formato html';