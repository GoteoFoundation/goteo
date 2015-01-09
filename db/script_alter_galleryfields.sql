-- Glossary

-- Campo calculado para imágenes de la galería
ALTER TABLE `glossary` ADD `gallery` VARCHAR( 2000 ) NULL COMMENT 'Galería de imagenes';

-- imagen principal
ALTER TABLE `glossary` ADD `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Imagen principal';


-- Info

-- Campo calculado para imágenes de la galería
ALTER TABLE `info` ADD `gallery` VARCHAR( 2000 ) NULL COMMENT 'Galería de imagenes';

-- imagen principal
ALTER TABLE `info` ADD `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Imagen principal';


-- Post

-- Campo calculado para imágenes de la galería
ALTER TABLE `post` ADD `gallery` VARCHAR( 2000 ) NULL COMMENT 'Galería de imagenes';


-- Proyecto

-- Campo calculado para imágenes de la galería  (mayor porque tiene secciones)
ALTER TABLE `project` ADD `gallery` VARCHAR( 10000 ) NULL COMMENT 'Galería de imagenes';

