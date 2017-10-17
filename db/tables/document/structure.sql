-- tabla para documentación de contrato
CREATE TABLE IF NOT EXISTS `document` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contract` varchar(50) COLLATE utf8_general_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
  `type` varchar(20) COLLATE utf8_general_ci DEFAULT NULL,
  `size` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


ALTER TABLE `document` ADD FOREIGN KEY (`contract`) REFERENCES `contract`(`project`) ON UPDATE CASCADE ON DELETE RESTRICT;

-- mime type for documents
ALTER TABLE `document` CHANGE `type` `type` VARCHAR(120) CHARSET utf8 COLLATE utf8_general_ci NULL;
