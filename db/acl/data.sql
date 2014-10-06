


-- obsoletos de /project/
DELETE FROM `acl` WHERE `acl`.`id` = 17;
DELETE FROM `acl` WHERE `acl`.`id` = 18;
DELETE FROM `acl` WHERE `acl`.`id` = 22;
DELETE FROM `acl` WHERE `acl`.`id` = 23;
DELETE FROM `acl` WHERE `acl`.`id` = 28;
DELETE FROM `acl` WHERE `acl`.`id` = 55;
DELETE FROM `acl` WHERE `acl`.`id` = 73;



-- obsoletos de convocatoria
DELETE FROM `acl` WHERE `acl`.`id` = 63;
DELETE FROM `acl` WHERE `acl`.`id` = 66;
DELETE FROM `acl` WHERE `acl`.`id` = 68;
DELETE FROM `acl` WHERE `acl`.`id` = 69;
DELETE FROM `acl` WHERE `acl`.`id` = 70;
DELETE FROM `acl` WHERE `acl`.`id` = 84;

-- extraña restricción en /translate/node
DELETE FROM `acl` WHERE `acl`.`id` = 76;


-- eliminamos los temporales
DELETE FROM `acl` WHERE id > 999;
