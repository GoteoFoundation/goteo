CREATE TABLE IF NOT EXISTS lang (
  id varchar(2) NOT NULL COMMENT 'Código ISO-639',
  `name` varchar(20) NOT NULL,
  `active` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Idiomas';


-- alters
ALTER TABLE `lang` ADD `short` VARCHAR( 10 ) NULL;