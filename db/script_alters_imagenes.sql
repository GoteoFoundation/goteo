

-- bazaar (aplicado en beta)
ALTER TABLE `bazar` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';
-- script
UPDATE bazar SET image = (SELECT name FROM image WHERE id = image) WHERE image IS NOT NULL AND image REGEXP '[0-9]+';



-- banner (aplicado en beta)
ALTER TABLE `banner` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';
-- script
UPDATE banner SET image = (SELECT name FROM image WHERE id = image) WHERE image IS NOT NULL AND image REGEXP '[0-9]+';



-- sponsor (aplicado en beta)
ALTER TABLE `sponsor` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';
-- script
UPDATE sponsor SET image = (SELECT name FROM image WHERE id = image);





-- call_banner
ALTER TABLE `call_banner` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

-- script
UPDATE call_banner SET image = (SELECT name FROM image WHERE id = image ) WHERE image IS NOT NULL AND image REGEXP '[0-9]+';

-- call_sponsor
ALTER TABLE `call_sponsor` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

-- script
UPDATE call_sponsor SET image = (SELECT name FROM image WHERE id = image) WHERE image IS NOT NULL AND image REGEXP '[0-9]+';

-- feed
ALTER TABLE `feed` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

-- script
UPDATE feed SET image = (SELECT name FROM image WHERE id = image) WHERE image IS NOT NULL AND image REGEXP '[0-9]+';

-- glossary_image
ALTER TABLE `glossary_image` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

-- script
UPDATE glossary_image SET image = (SELECT name FROM image WHERE id = image) WHERE image IS NOT NULL AND image REGEXP '[0-9]+';

-- info_image
ALTER TABLE `info_image` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

-- script
UPDATE info_image SET image = (SELECT name FROM image WHERE id = image) WHERE image IS NOT NULL AND image REGEXP '[0-9]+';

-- news
 ALTER TABLE `news` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

 -- script
UPDATE news SET image = (SELECT name FROM image WHERE id = image) WHERE image IS NOT NULL AND image REGEXP '[0-9]+';

-- post
 ALTER TABLE `post` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

 -- script
 UPDATE post SET image = (SELECT name FROM image WHERE id = image) WHERE image IS NOT NULL AND image REGEXP '[0-9]+';

 -- post_image
 ALTER TABLE `post_image` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

 -- script
UPDATE post_image SET image = (SELECT name FROM image WHERE id = image) WHERE image IS NOT NULL AND image REGEXP '[0-9]+';

 -- project
 ALTER TABLE `project` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

 -- script
UPDATE project SET image = (SELECT name FROM image WHERE id = image) WHERE image IS NOT NULL AND image REGEXP '[0-9]+';

 -- project_image
 ALTER TABLE `project_image` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

 -- script
UPDATE project_image SET image = (SELECT name FROM image WHERE id = image) WHERE image IS NOT NULL AND image REGEXP '[0-9]+';

 -- stories
 ALTER TABLE `stories` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

 -- script
UPDATE stories SET image = (SELECT name FROM image WHERE id = image) WHERE image IS NOT NULL AND image REGEXP '[0-9]+';

 -- user_image
 ALTER TABLE `user_image` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

 -- script
UPDATE user_image SET image = (SELECT name FROM image WHERE id = image) WHERE image IS NOT NULL AND image REGEXP '[0-9]+';

 -- user_vip
 ALTER TABLE `user_vip` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

 -- script
UPDATE user_vip SET image = (SELECT name FROM image WHERE id = image) WHERE image IS NOT NULL AND image REGEXP '[0-9]+';

 -- user
 ALTER TABLE `user` CHANGE `avatar` `avatar` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

 -- script
UPDATE user SET avatar = (SELECT name FROM image WHERE id = user.avatar) WHERE avatar IS NOT NULL AND avatar REGEXP '[0-9]+';

 -- node
 ALTER TABLE `node` CHANGE `logo` `logo` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

 -- script
UPDATE node SET logo = (SELECT name FROM image WHERE id = node.logo) WHERE logo IS NOT NULL AND logo REGEXP '[0-9]+';







