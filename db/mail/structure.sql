CREATE TABLE `mail` (
`id` SERIAL NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`email` TINYTEXT NOT NULL ,
`html` LONGTEXT NOT NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'Contenido enviado por email para el -si no ves-';


-- alters
ALTER TABLE `mail` ADD `template` int( 20 ) NULL ,
ADD `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ;

ALTER TABLE `mail` ADD `node` VARCHAR( 50 ) NULL AFTER `template` ;

-- para verificaciones de idioma alternativo
ALTER TABLE `mail`  ADD `lang` VARCHAR(2) NULL DEFAULT NULL COMMENT 'Idioma en el que se solicitó la plantilla'

-- almacenamiento del html en Amazon S3
ALTER TABLE `mail` ADD `content` VARCHAR(50) NULL DEFAULT NULL COMMENT 'ID del archivo con HTML estático';

-- cuando se pueda quitar...
ALTER TABLE `mail` DROP `html`;

-- constrains
ALTER TABLE `mail` ADD FOREIGN KEY (`node`) REFERENCES `node`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
UPDATE mail SET template=NULL WHERE template=0;
ALTER TABLE `mail` CHANGE `template` `template` BIGINT(20) UNSIGNED NULL, ADD FOREIGN KEY (`template`) REFERENCES `template`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE `mail` CHANGE `email` `email` CHAR(255) CHARSET utf8 COLLATE utf8_general_ci NOT NULL, ADD UNIQUE INDEX (`id`, `email`);
