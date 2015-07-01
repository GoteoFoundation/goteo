CREATE TABLE `node_lang` (
  `id` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `subtitle` text,
  `description` text,
  `pending` int(1) DEFAULT '0' COMMENT 'Debe revisarse la traducción',
  UNIQUE KEY `id_lang` (`id`,`lang`),
  CONSTRAINT `node_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8