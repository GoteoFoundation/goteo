

-- bazaar
ALTER TABLE `bazar` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

-- script
UPDATE bazar SET image = (SELECT name FROM image WHERE id = image);

-- banner
ALTER TABLE `banner` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

-- script
UPDATE banner SET image = (SELECT name FROM image WHERE id = image);

-- call_banner
ALTER TABLE `call_banner` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

-- script
UPDATE call_banner SET image = (SELECT name FROM image WHERE id = image);

-- call_sponsor
ALTER TABLE `call_sponsor` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

-- script
UPDATE call_sponsor SET image = (SELECT name FROM image WHERE id = image);

-- feed
ALTER TABLE `feed` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

-- script
UPDATE feed SET image = (SELECT name FROM image WHERE id = image);

-- glossary_image
ALTER TABLE `glossary_image` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

-- script
UPDATE glossary_image SET image = (SELECT name FROM image WHERE id = image);

-- info_image
ALTER TABLE `info_image` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

-- script
UPDATE info_image SET image = (SELECT name FROM image WHERE id = image);

-- news
 ALTER TABLE `news` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

 -- script
UPDATE news SET image = (SELECT name FROM image WHERE id = image);

-- post
 ALTER TABLE `post` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

 -- script
 UPDATE post SET image = (SELECT name FROM image WHERE id = image);

 -- post_image
 ALTER TABLE `post_image` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

 -- script
UPDATE post_image SET image = (SELECT name FROM image WHERE id = image);

 -- project
 ALTER TABLE `project` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

 -- script
UPDATE project SET image = (SELECT name FROM image WHERE id = image);

 -- project_image
 ALTER TABLE `project_image` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

 -- script
UPDATE project_image SET image = (SELECT name FROM image WHERE id = image);

 -- sponsor
 ALTER TABLE `sponsor` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

 -- script
UPDATE sponsor SET image = (SELECT name FROM image WHERE id = image);

 -- stories
 ALTER TABLE `stories` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

 -- script
UPDATE stories SET image = (SELECT name FROM image WHERE id = image);

 -- user_image
 ALTER TABLE `user_image` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

 -- script
UPDATE user_image SET image = (SELECT name FROM image WHERE id = image);

 -- user_vip
 ALTER TABLE `user_vip` CHANGE `image` `image` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

 -- script
UPDATE user_vip SET image = (SELECT name FROM image WHERE id = image);

 -- user
 ALTER TABLE `user` CHANGE `avatar` `avatar` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

 -- script
UPDATE user SET avatar = (SELECT image FROM image WHERE id = avatar);

 -- node
 ALTER TABLE `node` CHANGE `logo` `logo` VARCHAR( 255 ) NULL DEFAULT NULL COMMENT 'Contiene nombre de archivo';

 -- script
UPDATE node SET logo = (SELECT image FROM image WHERE id = logo);







