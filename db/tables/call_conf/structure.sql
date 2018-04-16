CREATE TABLE `call_conf` (
`call` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
`limit1` SET('normal', 'minimum', 'unlimited', 'none') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'normal' COMMENT 'tipo limite riego primera ronda',
`limit2` SET('normal', 'minimum', 'unlimited', 'none') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'none' COMMENT 'tipo limite riego segunda ronda',
`buzz_first` INT(1) NOT NULL DEFAULT '0' COMMENT 'Solo primer hashtag en el buzz',
`buzz_own` INT(1) NOT NULL DEFAULT '1' COMMENT 'Tweets  propios en el buzz',
`buzz_mention` INT(1) NOT NULL DEFAULT '1' COMMENT 'Menciones en el buzz',
PRIMARY KEY (`call`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Configuración de convocatoria';

-- Y este se me olvidaba
ALTER TABLE `call_conf`  ADD `applied` INT(4) NULL DEFAULT NULL COMMENT 'Para fijar numero de proyectos recibidos' AFTER `call`;

ALTER TABLE `call_conf` ADD FOREIGN KEY (`call`) REFERENCES `call`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
