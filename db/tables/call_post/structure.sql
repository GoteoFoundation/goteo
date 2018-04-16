CREATE TABLE IF NOT EXISTS call_post (
  `call` varchar(50) NOT NULL,
  post int(20) NOT NULL,
  UNIQUE KEY call_post (`call`,post)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Entradas de blog asignadas a convocatorias';

ALTER TABLE `call_post` CHANGE `post` `post` BIGINT(20) UNSIGNED NOT NULL, ADD FOREIGN KEY (`call`) REFERENCES `call`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, ADD FOREIGN KEY (`post`) REFERENCES `post`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
