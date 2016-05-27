CREATE TABLE IF NOT EXISTS `invest_msg` (
  `invest` bigint(20) unsigned NOT NULL,
  `msg` text,
  PRIMARY KEY (`invest`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Mensaje de apoyo al proyecto tras aportar';


ALTER TABLE `invest_msg` ADD FOREIGN KEY (`invest`) REFERENCES `invest`(`id`) ON UPDATE CASCADE;
