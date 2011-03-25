CREATE TABLE IF NOT EXISTS `purpose` (
  `text` varchar(50) NOT NULL,
  `purpose` tinytext NOT NULL,
  PRIMARY KEY (`text`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Explicación del propósito de los textos';