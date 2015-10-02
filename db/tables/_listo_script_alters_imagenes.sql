-- NOTA
-- Solo alters en el tipo de campo que contendrá el nombre de la imagen
-- despues de pasar este script se puede ir poniendo $image->newstyle en el save para que no se guarden más


-- bazaar (aplicado en beta)
ALTER TABLE `bazar` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';



-- banner (aplicado en beta)
ALTER TABLE `banner` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';


-- sponsor (aplicado en beta)
ALTER TABLE `sponsor` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';


-- call (logo)
ALTER TABLE `call` CHANGE `logo` `logo` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Logo. Contiene nombre de archivo';

-- call (imagen widget)
ALTER TABLE `call` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Imagen widget. Contiene nombre de archivo';

-- call (background)
ALTER TABLE `call` CHANGE `backimage` `backimage` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Imagen background. Contiene nombre de archivo';

-- call_banner
ALTER TABLE `call_banner` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

-- call_sponsor
ALTER TABLE `call_sponsor` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

-- feed
ALTER TABLE `feed` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

-- glossary_image
ALTER TABLE `glossary_image` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

-- info_image
ALTER TABLE `info_image` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';


-- news
ALTER TABLE `news` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

-- post
ALTER TABLE `post` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';



-- post_image
ALTER TABLE `post_image` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';



-- project
ALTER TABLE `project` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';


-- project_image
ALTER TABLE `project_image` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';



-- stories
ALTER TABLE `stories` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';


-- user_vip
ALTER TABLE `user_vip` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';


-- user
ALTER TABLE `user` CHANGE `avatar` `avatar` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

-- node
ALTER TABLE `node` CHANGE `logo` `logo` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';





