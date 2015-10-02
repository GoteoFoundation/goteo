-- NOTA:
-- Estos comandos son solo para actualizar nombres de imagen en las tablas (alters en otro script)
-- Ya está aplicado hasta real
-- no necesario lanzar mas, mantenido como histórico


-- bazaar
-- script
UPDATE bazar SET image = (SELECT name FROM image WHERE id = image) WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$';



-- banner
-- script
UPDATE banner SET image = (SELECT name FROM image WHERE id = image) WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$';


-- sponsor
-- script
UPDATE sponsor SET image = (SELECT name FROM image WHERE id = image) WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$';



-- call (logo)
-- script
UPDATE `call` SET logo = (SELECT name FROM image WHERE id = logo ) WHERE logo IS NOT NULL AND logo REGEXP '^[0-9]+$';


-- call (imagen widget)
-- script
UPDATE `call` SET image = (SELECT name FROM image WHERE id = image ) WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$';


-- call (background)
-- script
UPDATE `call` SET backimage = (SELECT name FROM image WHERE id = backimage ) WHERE backimage IS NOT NULL AND backimage REGEXP '^[0-9]+$';





-- call_banner
-- script
UPDATE call_banner SET image = (SELECT name FROM image WHERE id = image ) WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$';



-- call_sponsor
-- script
UPDATE call_sponsor SET image = (SELECT name FROM image WHERE id = image) WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$';



-- feed

-- script
UPDATE feed SET image = (SELECT name FROM image WHERE id = image) WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$';



-- glossary_image (esta tabla tiene un UNIQUE para id-image, antes de pasar el update eliminamos los registros sin elemento en la tabla image)
DELETE FROM `glossary_image` WHERE glossary_image.image REGEXP '^[0-9]+$' AND glossary_image.image NOT IN (SELECT id FROM image);
-- script
UPDATE glossary_image SET image = (SELECT name FROM image WHERE id = image) WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$';



-- info_image (esta tabla tiene un UNIQUE para id-image, antes de pasar el update eliminamos los registros sin elemento en la tabla image)
DELETE FROM `info_image` WHERE info_image.image REGEXP '^[0-9]+$' AND info_image.image NOT IN (SELECT id FROM image);
-- script
UPDATE info_image SET image = (SELECT name FROM image WHERE id = image) WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$';



-- news
-- script
UPDATE news SET image = (SELECT name FROM image WHERE id = image) WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$';



-- post
-- script
UPDATE post SET image = (SELECT name FROM image WHERE id = image) WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$';



-- post_image (esta tabla tiene un UNIQUE para id-image, antes de pasar el update eliminamos los registros sin elemento en la tabla image)
DELETE FROM `post_image` WHERE post_image.image REGEXP '^[0-9]+$' AND post_image.image NOT IN (SELECT id FROM image);
-- script
UPDATE post_image SET image = (SELECT name FROM image WHERE id = image) WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$';



-- project
-- script
UPDATE project SET image = (SELECT name FROM image WHERE id = image) WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$';


-- project_image (esta tabla tiene un UNIQUE para id-image, antes de pasar el update eliminamos los registros sin elemento en la tabla image)
DELETE FROM `project_image` WHERE project_image.image REGEXP '^[0-9]+$' AND project_image.image NOT IN (SELECT id FROM image);
-- script
UPDATE project_image SET image = (SELECT name FROM image WHERE id = image AND name != '') WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$';



-- stories
-- script
UPDATE stories SET image = (SELECT name FROM image WHERE id = image) WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$';



-- user_vip
-- script
UPDATE user_vip SET image = (SELECT name FROM image WHERE id = image) WHERE image IS NOT NULL AND image REGEXP '^[0-9]+$';



-- user
-- script
UPDATE user SET avatar = (SELECT name FROM image WHERE id = user.avatar) WHERE avatar IS NOT NULL AND avatar REGEXP '^[0-9]+$';



-- node
-- script
UPDATE node SET logo = (SELECT name FROM image WHERE id = node.logo) WHERE logo IS NOT NULL AND logo REGEXP '^[0-9]+$';





